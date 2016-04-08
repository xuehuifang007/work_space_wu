/**msg.cityMap.alertMsg.directive.js
 * 命名名称注释:directive 简称_msg,父模块_dipan . 功能_鼠标悬停协议列表地址信息栏,弹出地址详细信息框. 类型_directive .js
 * 使用 ：<div msg></div>
 * Created by xuehuifang on 16/4/5.
 */
(function () {
    'use strict';

    angular.module('cityMap').directive('msg',msg);
    function msg() {

        return {
            restrict: 'A',
            replace: false,
            scope: {},
            templateUrl: 'Public/resCreate/html/src/public/areaKey/msg.cityMap.alertMsg.directive.html',
            controller: thisController,
            link: function (scope, element, attrs) {

            }
        };
    }

    thisController.$inject = ['$scope'];

    function thisController($scope) {

    }


})();