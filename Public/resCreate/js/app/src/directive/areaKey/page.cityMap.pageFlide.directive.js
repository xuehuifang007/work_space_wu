/**
 * page.cityMap.pageFlide.directive.js
 * 命名名称注释:directive简称_page,父模块_dipan . 功能_顶部_显示5条协议列表数据,实现页码跳转. 类型_directive .js
 * 使用 ：<div page></div>
 * Created by xuehuifang on 16/4/5.
 */
(function () {
    'use strict';

    angular.module('cityMap').directive('page', page);
    function page() {

        return {
            restrict: 'A',
            replace: false,
            scope: {},
            templateUrl: 'Public/resCreate/html/src/public/areaKey/page.cityMap.pageFlide.directive.html',
            controller: thisController,
            link: function (scope, element, attrs) {

            }
        };
    }

    thisController.$inject = ['$scope'];

    function thisController($scope) {

    }


})();