/**
 * compile.uiBlock.compile.factory.js
 * 命名注释：server简称_compile. 父模块 uiBlock . 功能_动态绑定html元素到angular. 类型_factory.js
 * 传入 需要append的id，html内容 ，scope
 * Created by rockblus on 16-2-13.
 */

(function () {
    'use strict';
    angular.module('cityMap').factory('compile', compile);

    compile.$inject = ['$compile'];


    function compile($compile) {
        function _compile(domId, htmlStr, scope) {
            try {
                var reBindContent = document.getElementById(domId);
                reBindContent = angular.element(reBindContent);
                reBindContent.html('');
                var el = $compile(htmlStr)(scope);
                reBindContent.append(el);
            } catch (e) {
                console.error(e);
            }
        }

        return _compile;
    }


})();

