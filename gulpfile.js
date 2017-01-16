'use strict';

var browserify = require('browserify');
var gulp = require('gulp');
var source = require('vinyl-source-stream');
var uglify = require('gulp-uglify');
var buffer = require('vinyl-buffer');
var watchify = require('watchify');
var parsePath = require('parse-filepath');
var gutil = require('gulp-util');
var less = require('gulp-less');
var rename = require('gulp-rename');
var randomstring = require('randomstring');
var jeditor = require('gulp-json-editor');

var minifyCss = require('gulp-minify-css');
var sourcemaps = require('gulp-sourcemaps');
var del = require('del');
var path = require('path');

var symlink = require('gulp-sym');

var runSequence = require('run-sequence');

var cachebuster = randomstring.generate(20);


// map that tells us which output files have been updated
var modified = {};

/*
	JS
 */
function doBrowserify(sourceFile, destFile, watch) {

	modified[destFile] = true;

	var bundler;
	var sourceinfo = parsePath(sourceFile);
	var destinfo = parsePath(destFile);

	if (watch) {
		var opts = watchify.args;
		opts.entries = [sourceFile];
		opts.debug = true;
		bundler = watchify(browserify(opts)).on('update', rebundle)
		.on('log', function(msg) {
			gutil.log('Browserify:', msg);
		});
	} else {
		bundler = browserify(sourceFile);
	}

	function rebundle() {

		return bundler
			.bundle()
			.on('error', function(err) {
				gutil.log('Browserify:', err.toString());
				this.emit('end');
			})
			.pipe(source(sourceinfo.basename))
			.pipe(buffer())
			.pipe((!watch ? uglify() : gutil.noop()))
			.pipe(rename(destinfo.name + "-" + cachebuster + destinfo.extname))
			.pipe(gulp.dest(path.resolve('public', 'js')));
	}

	return rebundle();
}


gulp.task('browserify', function() {
	return doBrowserify(path.resolve('resources', 'js', 'bonsumApp.js'), 'bundle.js');
});

gulp.task('browserify-admin', function() {
	return doBrowserify(path.resolve('resources', 'js', 'bonsumAdminApp.js'), 'admin-bundle.js');
});

gulp.task('browserify-watch', function() {
	return doBrowserify(path.resolve('resources', 'js', 'bonsumApp.js'), 'bundle.js', true);
});

gulp.task('browserify-admin-watch', function() {
	return doBrowserify(path.resolve('resources', 'js', 'bonsumAdminApp.js'), 'admin-bundle.js', true);
});

gulp.task('js', function() {
	runSequence('clean:js',
		['browserify', 'browserify-admin'],
		'manifest'
	);
});

gulp.task('watch:js', function() {

	runSequence('clean:js',
		['browserify-watch', 'browserify-admin-watch'],
		'manifest'
	);
});



/*
	LESS
 */
function compileLess(src, dest, watch) {

	modified[dest] = true;
	var destinfo = parsePath(dest);

	return gulp.src(src)
	.pipe(watch ? sourcemaps.init() : gutil.noop())
	.pipe(less()).on('error', function(err) {
		gutil.log(err.toString());
		this.emit('end');
	})
	.pipe(watch ? gutil.noop() : minifyCss())
	.pipe(watch ? sourcemaps.write() : gutil.noop())
	.pipe(rename(destinfo.name + "-" + cachebuster + destinfo.extname))
	.pipe(gulp.dest(path.resolve('public', 'css')));
}

gulp.task('less', function() { return compileLess(path.resolve('resources', 'assets', 'less', 'main.less'), 'bundle.css', false); });
gulp.task('less-watch', function() { return compileLess(path.resolve('resources', 'assets', 'less', 'main.less'), 'bundle.css', true); });
gulp.task('less-admin', function() { return compileLess(path.resolve('resources', 'assets', 'less', 'main-admin.less'), 'admin-bundle.css', false); });
gulp.task('less-admin-watch', function() { return compileLess(path.resolve('resources', 'assets', 'less', 'main-admin.less'), 'admin-bundle.css', true); });




/*
	FONTS
 */
gulp.task('font-awesome-fonts', function() {
	return gulp.src(path.resolve('vendor', 'fortawesome', 'font-awesome', 'fonts', '**', '*'))
	.pipe(gulp.dest(path.resolve('public', 'fonts')));
});
gulp.task('open-sans-fonts', function() {
	return gulp.src(path.resolve('node_modules', 'open-sans-fontface', 'fonts', '**', '*'))
	.pipe(gulp.dest(path.resolve('public', 'css', 'fonts')));
});

gulp.task('bootstrap-fonts', function() {
	return gulp.src(path.resolve('vendor','twitter','bootstrap','fonts','**', '*'))
	.pipe(gulp.dest(path.resolve('public', 'fonts')));
});
gulp.task('other-fonts', function() {
	return gulp.src(path.resolve('resources', 'assets','fonts','**','*'))
	.pipe(gulp.dest(path.resolve('public','fonts')));
});
gulp.task('fonts', ['bootstrap-fonts', 'font-awesome-fonts', 'open-sans-fonts', 'other-fonts']);



/*
	GENERAL TASKS
 */
gulp.task('manifest', function() {

	// manifest.json must exist for this to work
	return gulp.src(path.resolve('resources', 'manifest.json'))
	.pipe(jeditor(function(json) {
		for (var filename in modified) {
			var fileinfo = parsePath(filename);
			if (modified[filename]) {
				json[filename] = fileinfo.name + "-" + cachebuster + fileinfo.extname;
			}
		}
		return json;
	})).on('error', function(err) {
		gutil.log(err.toString());
		this.emit('end');
	})
	.pipe(gulp.dest('resources'));
});

gulp.task('styles', function() {
	runSequence(
		['clean:css'],
		['less', 'less-admin', 'fonts'],
		'manifest'
	);
});
gulp.task('watch:css', function() {
	gulp.watch(path.resolve('resources', 'assets', '**', '*'), function() {
		runSequence('clean:css',
			['less-watch', 'less-admin-watch'],
			'manifest'
		);
	});
});

gulp.task('watch', ['watch:js', 'watch:css', 'symlinks']);

gulp.task('symlinks', function() {

	gulp.src(path.resolve('vendor','ckeditor','ckeditor'))
	.pipe(symlink(path.resolve('public','js','ckeditor'), { force: true} ));

/*	gulp.src(path.resolve('node_modules','zeroclipboard','dist','ZeroClipboard.swf'))
	.pipe(symlink(path.resolve('public','js','ZeroClipboard.swf'), { force: true}))*/
});


/*
	DEFAULT TASK
 */
gulp.task('default', ['js', 'styles', 'symlinks']);

/*
	CLEAN UP TASK
*/
gulp.task('clean:css', function(cb) {
	del(path.resolve('public','css','*.css'));
	cb();
});
gulp.task('clean:fonts', function(cb) {
	del([path.resolve('public','fonts','**','*'), path.resolve('public','css','fonts','**','*')]);
	cb();
});
gulp.task('clean:js', function(cb) {
	del(path.resolve('public','js','*.js'));
	cb();
});


gulp.task('clean:all', ['clean:css', 'clean:js', 'clean:fonts']);



