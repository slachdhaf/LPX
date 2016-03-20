$(document).ready(function () {
    "use strict";
    $("#stats a").click(function () {
        $(function () {
            $("#linearChart").find("svg").remove();
            $("#linearChart").drawLinearChart();
        });
    });
});

(function ($, undefined) {
    $.fn.drawLinearChart = function (options) {
        var $this = this,
            W = $this.width(),
            H = $this.height(),
            settings = $.extend({
                segmentStrokeWidth : 2,
                animation : true,
                animationSteps : 90,
                animationEasing : "easeInOutExpo",
                beforeDraw: function () {  },
                afterDrawed : function () {  },
                onPieMouseenter : function (e, data) {  },
                onPieMouseleave : function (e, data) {  },
                onPieClick : function (e, data) {  }
            }, options),
            animationOptions = {
                linear : function (t) {
                    return t;
                },
                easeInOutExpo: function (t) {
                    var v = t < 0.5 ? 8 * t * t * t * t : 1 - 8 * (--t) * t * t * t;
                    return (v > 1) ? 1 : v;
                }
            },
            easingFunction = animationOptions[settings.animationEasing],
            requestAnimFrame = function () {
                return window.requestAnimationFrame ||
                    window.webkitRequestAnimationFrame ||
                    window.mozRequestAnimationFrame ||
                    window.oRequestAnimationFrame ||
                    window.msRequestAnimationFrame ||
                    function (callback) {
                        window.setTimeout(callback, 1000 / 60);
                    };
            }();

        var $wrapper = $('<svg width="' + W + '" height="' + H + '" viewBox="0 0 ' + W + ' ' + H + '" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"></svg>').appendTo($this).css("overflow", "visible");

        var $groups = [],
            $segments = [],
            $points = [],
            $curves = $this.find("div");

        var maxVLabels = 0;
        $curves.each(function () {
            var data = $(this).data().value.split(" ");

            for (var i = 0; i < data.length; i++)
                data[i] = parseInt(data[i], 10);

            var max = Math.max.apply(Math,data);
            if(maxVLabels < max) maxVLabels = max;
        })

        var colors = Please.make_color({
            base_color: 'deepskyblue',
            golden: false, //disable default
            saturation: 0.7, //set your saturation manually
            value: 0.8, //set your value manually
            colors_returned: $curves.length //set number of colors returned
        });

        var $hLabels = $(document.createElementNS('http://www.w3.org/2000/svg', 'g')).appendTo($wrapper);
        var hLabels = $this.data().value.split(" ");
        var $vLabels = $(document.createElementNS('http://www.w3.org/2000/svg', 'g')).appendTo($wrapper);

        var nbPoints = 5;
        for (var i = 0; i < nbPoints; i++){
            var value = Math.round((maxVLabels - (maxVLabels/nbPoints)*i)*100)/100;
            var fo = document.createElementNS('http://www.w3.org/2000/svg', 'foreignObject');
            fo.setAttribute("width", 30);
            fo.setAttribute("height", 15);
            fo.setAttribute("x", -35);
            fo.setAttribute("y", H-(H*value/maxVLabels)-5);
            $("<span>" + value + "</span>").appendTo(fo);
            $(fo).appendTo($vLabels).css("text-align", "right");

            var l = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            l.setAttribute("x1", 0);
            l.setAttribute("y1", H-(H*value/maxVLabels));
            l.setAttribute("x2", W);
            l.setAttribute("y2", H-(H*value/maxVLabels));
            l.setAttribute("stroke-dasharray", 3);
            l.setAttribute("stroke-width", 1);
            l.setAttribute("stroke", "rgba(255, 255, 255, 0.5)");
            $(l).appendTo($vLabels);
        }

        $.each(hLabels, function (index, value) {
            var fo = document.createElementNS('http://www.w3.org/2000/svg', 'foreignObject');
            fo.setAttribute("width", 30);
            fo.setAttribute("height", 15);
            fo.setAttribute("x", (W/(hLabels.length-1))*index-5);
            fo.setAttribute("y", H + 5);
            $("<span>" + value + "</span>").appendTo(fo);
            $(fo).appendTo($hLabels);
        });
        
        var i = 0;
        $curves.each(function (){

            var g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            $groups[i] = $(g).appendTo($wrapper);

            var p = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            p.setAttribute("stroke-width", settings.segmentStrokeWidth);
            p.setAttribute("stroke", colors[i]);
            p.setAttribute("fill", "none");
            $segments[i] = $(p).appendTo($groups[i]);

            var pointsX = $this.data().value.split(" ");
            var pointsY = $(this).data().value.split(" ");
            $points[i]= [];

            for (var j = 0; j < pointsY.length; j++){
                $points[i].push([W*pointsX[j]/(pointsX.length-1),H-(pointsY[j]*H/maxVLabels)])
            }

            for (var j = 0; j < $points[i].length; j++){
                var p = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                p.setAttribute("class", "curvePoint");
                p.setAttribute("cx", $points[i][j][0]);
                p.setAttribute("cy", $points[i][j][1]);
                p.setAttribute("fill", colors[i]);
                p.setAttribute("r", "3");
                $(p).appendTo($groups[i]).hover(function (){
                    
                });
            }

            i++;
        });

        function drawLineSegments (animationDecimal){

            for (var i = 0, len = $segments.length; i < len; i++){
                var points = $points[i];

                var cmd = ['M', points[0][0]+','+points[0][1]]//Move pointer

                var f0 = function(e, idx){
                    var a = (points[idx][1] - points[idx-1][1])/(points[idx][0] - points[idx-1][0]);
                    var b = points[idx-1][1] - a * points[idx-1][0];
                    return a*e + b;
                };

                var f = function(e,idx){
                    if(idx == 0){
                        return f0(e,idx+1);
                    }
                    else if(idx == points.length-1){
                        return f0(e, idx);
                    }
                    else{
                        var a = (points[idx+1][1] - points[idx-1][1])/(points[idx+1][0] - points[idx-1][0])
                        var b = points[idx][1] - a * points[idx][0];
                        return a*e + b;
                    }
                };

                for (var j = 1; j < points.length; j++){
                    var p = (points[j-1][0] + points[j][0])/2;
                    cmd.push('C' 
                             + p + ',' + (f(p, j-1) * animationDecimal + H * (1-animationDecimal)) + ' ' 
                             + p + ',' + (f(p,j)    * animationDecimal + H * (1-animationDecimal)) + ' ' 
                             + points[j][0] + ',' + (points[j][1] * animationDecimal + H * (1-animationDecimal)));
                }
                $segments[i][0].setAttribute("d",cmd.join(' '));
            }
        }

        var animFrameAmount = (settings.animation)? 1/settings.animationSteps : 1,//if settings.animationSteps is 10, animFrameAmount is 0.1
            animCount =(settings.animation)? 0 : 1;

        function triggerAnimation(){
            if (settings.animation) {
                requestAnimFrame(animationLoop);
            } else {
                drawLineSegments(1);
            }
        }
        function animationLoop(){
            animCount += animFrameAmount;//animCount start from 0, after "settings.animationSteps"-times executed, animCount reaches 1.
            drawLineSegments(easingFunction(animCount));
            if (animCount < 1){
                requestAnimFrame(arguments.callee);
            } else {
                settings.afterDrawed.call($this);
            }
        }

        function switchBenchmark(point, maxX, maxY){
            return [point[0], H-point[1]];
        }

        triggerAnimation();
        return $this;
    };
})(jQuery);