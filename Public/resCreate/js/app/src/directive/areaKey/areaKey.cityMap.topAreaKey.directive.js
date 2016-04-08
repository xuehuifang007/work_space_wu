/**
 * keyShow.cityMap.keyShowSelect.directive.js
 * 命名注释：directive简称_keyShow. 父模块_dipan . 功能_根据输入的协议编号,搜索定位到该协议当前位置. 类型_directive .js
 * 使用 ：<div key-show></div>
 * Created by rockblus
 */
(function () {
    'use strict';

    angular.module('cityMap').directive('keyShow', keyShow);


    //此处是 directive demo
    function keyShow() {

        return {
            restrict: 'A',
            replace: false,
            scope: {},
            templateUrl: 'Public/resCreate/html/src/public/areaKey/keyShow.cityMap.keyShowSelect.directive.html',
            controller: thisController,
            link: function (scope, element, attrs) {
            }
        };
    }

    thisController.$inject = ['$scope'];

    function thisController($scope) {
        $scope.topArea = 'ddddd88888';

        /**
         * 监听php的全局变量对象解析事件,来更新key 并使用bindOne从新绑定 这是 监听 rootscope 广播来的  事件,共享数据就是这样的方法,服务里面
         * 不必关心 广播到哪, 只在,子 directive 里面接收就可以了,(前提是 注入 服务)
         * 16/3/17 */
        $scope.$on('urlParseChange', function () {
            $scope.topArea = 'aaaa';
            //console.log('urlParse', urlParse);
        });


    }


})();
