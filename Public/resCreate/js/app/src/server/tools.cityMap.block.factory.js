/**
 *tools.dipan.block.factory.js
 * 命名注释：server简称_tools. 父模块 dipan . 功能_tools 相关服务:otherDiv. 类型_factory.js
 * otherDiv
 * Created by rockblus on 16-2-5.
 */

(function () {
    'use strict';
    angular.module('cityMap').factory('tools', tools);


    tools.$inject = ['$http'];

    function tools($http) {

        var re;

        re = {
            /**
             * alertDiv.alertInfo 在模板挂载了一个 点击事件传来的 attr。可以带到 alertInfo 的模板中，
             * 此处是返回这个 otherDiv 的module json串转换的对象
             * 16/2/18 */
            otherData: otherData,

            /********************
             * 验证相关
             * 16/2/18 ************************/
            /** 验证空 15-3-22 */
            isEmpty: isEmpty,


            /** 验证手机号 15-3-22 */
            checkMobile: checkMobile,

            /**
             * 删除数组中的 第几个元素
             * 16/2/18 */
            arrDel: arrDel,

            /**
             * postJsp
             * 16/2/19 */
            postJsp: postJsp,

            /**
             * 判断是否 function
             * 16/2/19 */
            isFunction: isFunction
        };

        /**
         * 具体fun *****************************
         * 16/2/18 */

        /**
         * alertDiv.alertInfo 在模板挂载了一个 点击事件传来的 attr。可以带到 alertInfo 的模板中，
         * 此处是回调这个 otherDiv 的module json串转换的对象
         * 16/2/18 */
        function otherData(fun) {
            var reContent = document.getElementById('otherData');
            if (reContent) {
                reContent = angular.element(reContent);
                setTimeout(function () {
                    reContent = reContent.attr('data');
                    reContent = JSON.parse(reContent);
                    fun(reContent);
                }, 0);
            }
        }

        /********************
         * 验证相关
         * 16/2/18 ************************/
        /** 验证空 15-3-22 */
        function isEmpty(t) {
            return t ? true : false;
        }

        /** 验证手机号 15-3-22 */
        function checkMobile(sMobile) {
            if (!(/^1[3|4|5|7|8][0-9]\d{4,8}$/.test(sMobile))) {
                return false;
            } else {
                return true;
            }
        }

        /**
         * 删除数组中的元素,传入数组，和第一个
         * 16/2/18 */
        function arrDel(arr, num) {
            if (!Array.prototype.remove) {
                Array.prototype.remove = function (from, to) {
                    var rest = this.slice((to || from) + 1 || this.length);
                    this.length = from < 0 ? this.length + from : from;
                    return this.push.apply(this, rest);
                };
            }
            arr.remove(num);
            delete Array.prototype.remove;
        }

        /**
         * angular post
         * 15-3-27 */
        function postJsp(getMoreUrl, data, re, errRe) {
            var endData = {};
            for (var vo in data) {
                endData[vo] = data[vo];
            }
            $http({
                url: getMoreUrl,
                method: "POST",
                data: endData,
                timeout: 10000 //超时设置
            }).success(function (response) {
                re(response);
            }).error(function (data, status, headers, config, error) {
                if (errRe) {
                    errRe();
                } else {
                    console.log('错误');
                }
                return false;
            });
        }

        /**
         * 判断是 function
         * 16/2/19 */
        function isFunction(fn) {
            return Object.prototype.toString.call(fn) === '[object Function]';
        }


        return re;
    }
})();

