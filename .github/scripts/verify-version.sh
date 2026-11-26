#!/usr/bin/env bash
# =============================================================================
# verify-version.sh — Release version-integrity gate
#
# SOURCES ENFORCED (all four must match EXPECTED):
#   1. Plugin header  — "Version:" line in ilove-pdf.php
#   2. Stable tag     — "Stable tag:" line in README.txt  (UPPERCASE filename)
#   3. Composer       — .version field in composer.json
#   4. Package        — .version field in package.json
#
# EXPECTED-DERIVATION ORDER (first matching rule wins):
#   1. GitHub release trigger  → $GITHUB_REF_NAME  (the release tag, v-prefix stripped)
#   2. Workflow-dispatch input → $INPUT_VERSION or $1  (passed explicitly)
#   3. Fallback                → plugin header Version: value
#
# FAIL-CLOSED: any mismatch, missing file, or extraction failure exits non-zero.
# Never echoes secrets; uses only tools preinstalled on ubuntu-latest (bash/grep/sed/jq).
# =============================================================================
set -euo pipefail

# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------

# normalize_version <value>
#   Trims surrounding whitespace and strips a single leading "v" or "V".
normalize_version() {
  local raw="$1"
  # strip leading/trailing whitespace
  local trimmed
  trimmed="$(printf '%s' "$raw" | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')"
  # strip a leading v/V
  printf '%s' "${trimmed#[vV]}"
}

# ---------------------------------------------------------------------------
# Determine EXPECTED version
# ---------------------------------------------------------------------------

PLUGIN_FILE="ilove-pdf.php"
README_FILE="README.txt"
COMPOSER_FILE="composer.json"
PACKAGE_FILE="package.json"

# Verify required files exist before attempting extraction
for f in "$PLUGIN_FILE" "$README_FILE" "$COMPOSER_FILE" "$PACKAGE_FILE"; do
  if [[ ! -f "$f" ]]; then
    echo "::error::Required file not found: $f (run from plugin root)"
    exit 1
  fi
done

# Extract plugin header version — anchored to the WordPress plugin header comment line only,
# matching " * Version: X.Y.Z" or "Version: X.Y.Z" at the start of the line (never a mid-line match).
PLUGIN_HEADER_RAW="$(grep -m1 -iE '^[[:space:]]*\*?[[:space:]]*Version:' "$PLUGIN_FILE" | sed -E 's/.*Version:[[:space:]]*//')"
PLUGIN_HEADER_VERSION="$(normalize_version "$PLUGIN_HEADER_RAW")"

if [[ -z "$PLUGIN_HEADER_VERSION" ]]; then
  echo "::error::Could not extract Version from $PLUGIN_FILE"
  exit 1
fi

# Determine EXPECTED
EXPECTED=""

if [[ "${GITHUB_EVENT_NAME:-}" == "release" && -n "${GITHUB_REF_NAME:-}" ]]; then
  EXPECTED="$(normalize_version "$GITHUB_REF_NAME")"
elif [[ -n "${INPUT_VERSION:-}" ]]; then
  # INPUT_VERSION env is the CI path; $1 positional is for local/manual invocation only.
  EXPECTED="$(normalize_version "$INPUT_VERSION")"
elif [[ -n "${1:-}" ]]; then
  # INPUT_VERSION env is the CI path; $1 positional is for local/manual invocation only.
  EXPECTED="$(normalize_version "$1")"
else
  EXPECTED="$PLUGIN_HEADER_VERSION"
fi

echo "Expected version: $EXPECTED"

# ---------------------------------------------------------------------------
# Extract all four sources
# ---------------------------------------------------------------------------

# 1. Plugin header (already extracted above)
SRC_PLUGIN="$PLUGIN_HEADER_VERSION"

# 2. README.txt stable tag (case-insensitive grep; tolerates leading whitespace)
README_RAW="$(grep -i 'Stable tag:' "$README_FILE" | head -1 | sed 's/.*[Ss]table [Tt]ag:[[:space:]]*//')"
SRC_README="$(normalize_version "$README_RAW")"
if [[ -z "$SRC_README" ]]; then
  echo "::error::Could not extract Stable tag from $README_FILE"
  exit 1
fi

# 3. composer.json
SRC_COMPOSER="$(normalize_version "$(jq -r '.version' "$COMPOSER_FILE")")"
if [[ -z "$SRC_COMPOSER" || "$SRC_COMPOSER" == "null" ]]; then
  echo "::error::Could not extract .version from $COMPOSER_FILE"
  exit 1
fi

# 4. package.json
SRC_PACKAGE="$(normalize_version "$(jq -r '.version' "$PACKAGE_FILE")")"
if [[ -z "$SRC_PACKAGE" || "$SRC_PACKAGE" == "null" ]]; then
  echo "::error::Could not extract .version from $PACKAGE_FILE"
  exit 1
fi

# ---------------------------------------------------------------------------
# Compare all sources to EXPECTED
# ---------------------------------------------------------------------------

MISMATCH=0

check_source() {
  local source_name="$1"
  local source_value="$2"
  if [[ "$source_value" != "$EXPECTED" ]]; then
    echo "::error::Version mismatch: $source_name is $source_value, expected $EXPECTED"
    MISMATCH=1
  fi
}

check_source "plugin header (ilove-pdf.php)"  "$SRC_PLUGIN"
check_source "stable tag (README.txt)"        "$SRC_README"
check_source "composer.json"                  "$SRC_COMPOSER"
check_source "package.json"                   "$SRC_PACKAGE"

# ---------------------------------------------------------------------------
# Result
# ---------------------------------------------------------------------------

if [[ "$MISMATCH" -ne 0 ]]; then
  exit 1
fi

echo "Version integrity check passed: all sources report $EXPECTED"
exit 0
