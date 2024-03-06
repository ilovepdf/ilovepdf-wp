import gulp from 'gulp';
import gulpSass from 'gulp-sass';
import cleanCSS from 'gulp-clean-css';
import autoprefixer from 'gulp-autoprefixer';
import * as sassCompiler from 'sass';
import uglify from 'gulp-uglify';
import rename from 'gulp-rename';
import merge from 'merge-stream';

const sass = gulpSass(sassCompiler);

// Task to compile Sass and minify CSS
gulp.task('build-css', function() {
    return gulp.src('dev/scss/**/*.scss')
        .pipe(sass.sync().on('error', sass.logError))
        .pipe(autoprefixer({
                overrideBrowserslist: ["last 2 versions"],
                cascade: false,
         }))
        .pipe(cleanCSS())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/css'));
});

// Task to compile and minify JavaScript
gulp.task('build-js', function() {
    // Process main.js in this file you can import others files js
    const mainJsStream = gulp.src('dev/js/main.js')
        .pipe(uglify())
        .pipe(rename({ suffix: '.min' })) // Rename file to main.min.js
        .pipe(gulp.dest('assets/js'));

    // Return a merged stream to signal completion
    return merge(mainJsStream);
});

// Task to watch for changes in Sass files
gulp.task('watch', function() {
    gulp.watch('dev/scss/**/*.scss', gulp.series('build-css'));
    gulp.watch('dev/js/**/*.js', gulp.series('build-js'));
});

// Default task
gulp.task('default', gulp.series('build-css', 'build-js', 'watch'));
