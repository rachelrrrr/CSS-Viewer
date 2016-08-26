<!DOCTYPE html>
<html lang="zh-CN">

<head>
<meta charset="UTF-8">
	<title>CSS Viewer</title>
	<link rel="stylesheet" type="text/css" href="css/index.css">
</head>
<body>
	<div id="container">
	<div class="logo">
		<img src="images/logo.png" alt="CSS Viwer logo">
	</div>
		<div class="setting-panel">
			<div class="style-box">
				<range :value.sync="translateX" :max="300"></range>
				<div class="style-info">translateX:{{ translateX.toFixed(2) }}px </div>
			</div>
			<div class="style-box">
				<range :value.sync="translateY" :max="300"></range>
				<div class="style-info">translateY:{{ translateY.toFixed(2) }}px </div>
			</div>
			<div class="style-box">
				<range :value.sync="skewX" :max="300"></range>
				<div class="style-info">skewX:{{ skewX.toFixed(2) }}deg </div>
			</div>
			<div class="style-box">
				<range :value.sync="skewY" :max="300"></range>
				<div class="style-info">skewY:{{ skewY.toFixed(2) }}deg </div>
			</div>
		</div>
		<div class="display-panel">
			<div id="displayBlock" :style="{ transform: styleTransform }"></div>
		</div>
		<template id="rangeTemplate">
			<div class="range" v-el:wrap>
				<div class="range-bg" v-el:bg></div>
				<div class="range-slider" :style="{ left:position + 'px' }" @mousedown="hold" v-el:slider></div>
			</div>
		</template>
	</div>
	<script src="js/vue.js"></script><!-- 
	<script src="http://cdn.bootcss.com/vue/1.0.26/vue.min.js"></script> -->
	<script>
		var componentRange = Vue.extend({
			template:"#rangeTemplate",
			data:function(){
				return {
					position:this.value,
					dragging:false,
					dragOffsetX:0
				};
			},
			props:{
				value:{
					type:Number,
					required:true,
					twoWay:true
				},
				max:{
					type:Number,
					default:100
				},
				min:{
					type:Number,
					default:0
				}
			},
			watch:{
				dragging:function(val){
					if(val){
						document.addEventListener("mousemove",this.drag,false);
						document.addEventListener("mouseup",this.release,false);
					}
					else{
						document.removeEventListener("mousemove",this.drag);
						document.removeEventListener("mouseup",this.release);
					}
				},
				/*
				* position的变化watch改为computed是否能提高可复用性
				*/
				position:function(val){
					this.value = (val / (this.$els.bg.clientWidth - this.$els.slider.clientWidth)) * this.max;
				}
			},
			methods:{
				hold:function(e){
					this.dragging = true;
					this.dragOffsetX = e.clientX - this.$els.wrap.offsetLeft - this.position;
				},
				drag:function(e){
					if(this.dragging){
						/*
						* 处于dragging状态时，判断光标位置进行不同处理
						* 1. 光标位置超出滑动条右侧，滑块停留在最右侧
						* 2. 光标位置超出滑动条左侧，滑块停留在最左侧
						* 3. 光标位置在滑动条范围内，滑块随光标移动
						*/
						if(e.clientX - this.$els.wrap.offsetLeft >= this.$els.bg.clientWidth){
							this.position = this.$els.bg.clientWidth - this.$els.slider.clientWidth;
						}
						else if(e.clientX - this.$els.wrap.offsetLeft <= this.dragOffsetX){
							this.position = 0;
						}
						else{
							this.position = e.clientX - this.$els.wrap.offsetLeft - this.dragOffsetX;
						}
					}
				},
				release:function(){
					this.dragging = false;
				}
			}
		})
		Vue.component("range",componentRange);
		var vm = new Vue({
			el:"#container",
			component:{
				"range":componentRange
			},
			data:{
				translateX:150,
				translateY:0,
				skewX:0,
				skewY:0,
				scale:0
			},
			computed:{
				styleTransform:function(){
					var translateX = "translateX(" + this.translateX + "px)",
						translateY = "translateY(" + this.translateY + "px)",
						skewX = "skewX(" + this.skewX + "deg)",
						skewY = "skewY(" + this.skewY + "deg)";
					return [translateX,translateY,skewX,skewY].join(" ");
				}
			}
		});
	</script>
</body>
</html>