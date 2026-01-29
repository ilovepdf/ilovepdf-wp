import { defineConfig } from 'eslint/config';
import config from 'eslint-config-prettier';

export default defineConfig(
    [
		config,
		{
			files: ['./dev/js/**/*.js'],
			languageOptions: {
				sourceType: 'module',
			},
			rules: {
				'no-console': ["error", { allow: ["warn", "error"] }],
				'no-unused-vars': 'warn',
				'no-unassigned-vars': 'error',
				'block-scoped-var': 'error',
				'no-empty': 'error',
				'no-loop-func': 'error',
				'no-multi-assign': 'error',
				'no-return-assign': 'error',
				'no-shadow-restricted-names': 'error',
				'no-var': 'error',
				'prefer-const': 'error',
				'no-cond-assign': 'error',
				'no-const-assign': 'error',
				'no-constant-condition': ['error', { checkLoops: 'all' }],
				'no-dupe-args': 'error',
				'no-dupe-else-if': 'error',
				'no-duplicate-imports': 'error',
				'no-duplicate-case': 'error',
				'no-dupe-keys': 'error',
				'no-func-assign': 'error',
				'no-import-assign': 'error',
				'no-invalid-regexp': 'error',
				'no-loss-of-precision': 'error',
				'no-self-compare': 'error',
				'no-sparse-arrays': 'error',
				'no-unreachable': 'error',
				'no-use-before-define': 'error',
				'no-useless-assignment': 'error',
				'use-isnan': 'error'
			}
		}
    ]
);
