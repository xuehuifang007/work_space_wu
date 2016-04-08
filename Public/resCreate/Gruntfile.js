module.exports = function (grunt) {

    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),

        /** 检查代码  */
        jshint: {
            //此处是 需要 测试 的文件路径，可自己修改
            build: ['js/app/src/**/*.js'],
            options: {
                //此处是 验证规则配置文件
                jshintrc: '.jshintrc'
            }
        },

        /**
         * 自动化
         * 16/1/22 */
        watch: {
            build: {
                files: ['Gruntfile.js', 'js/app/src/**/*.js','js/app/src/**/**/*.js', 'js/app/src/**/**/**/*.js','css/src/*.css','html/src/**/**/**/*.html','html/src/**/**/html','html/src/**/*.html'],
                tasks: ['jshint', 'ngtemplates', 'concat', 'uglify', 'cssmin','clean' ],//dist 配置
//                tasks: ['jshint', 'ngtemplates' ],//dev 配置
                options: {
                    spawn: false
                }
            }
        },

        /** angular 模板合并  */
        ngtemplates: {
            cityMap: {
                cwd: '../../',
                src: 'Public/resCreate/html/src/**/**.html',
                dest: 'js/app/dist/app.templates.js'
            }
        },

        /** js 文件合并 测试从头2 */
        concat: {
            option: {
                stripBanners: true
            },
            dist: {
                src: ['js/lib/bower_components/angular/angular.min.js',
                    'js/lib/bower_components/angular-ui-router/release/angular-ui-router.min.js',
                    'js/lib/bower_components/angular-bindonce/bindonce.min.js',
                    'js/app/src/**/*.js',
                    'js/app/src/**/**/*.js',
                    'js/app/src/**/**/**/*.js',
                    'js/app/dist/app.templates.js'
                ],//src文件夹下包括子文件夹下的所有文件
                dest: 'js/app/dist/app.js'//合并文件在dist下名为app.js的文件
            }
        },

        /**
         * 压缩 这个 合并后的 文件
         */
        uglify: {
            hellosea: {
                files: {
                    'js/app/dist/app.js': ['js/app/dist/app.js']
                }
            }
        },

        /** 删除app.templates.js  */
        clean: {
//            js: ['js/app/dist/app.templates.js'],
            css:['css/dist/*.min.css']
        },

        /**
         * 压缩css
         * 16/2/4 */
        cssmin: {
            target: {
                files: [
                    {
                        expand: true,
                        cwd: 'css/src',
                        src: ['*.css', '*.min.css'],
                        dest: 'css/dist',
                        ext: '.min.css'
                    }
                ]
            },
            options: {
                shorthandCompacting: false,
                roundingPrecision: -1
            },
            target2: {
                files: {
                    'css/dist/app.css': ['css/dist/*.min.css']
                }
            }
        }



    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-angular-templates');//angular 模板转js
    grunt.loadNpmTasks('grunt-contrib-cssmin');//css压缩
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('tpl', ['jshint', 'ngtemplates', 'concat', 'uglify', 'cssmin']);
};