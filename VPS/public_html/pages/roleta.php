<?php
// include_once('./include/head.php');
?>
<html>
<head>
	<style>
@import url('https://fonts.googleapis.com/css?family=Material+Icons|Work+Sans:400,700,900');

body{
  overflow: hidden;
}

$spin-duration: 2.5s;
$spin-easing: cubic-bezier(0,0.4,0.4,1.025);

$size: 400px;
$button-size: 12em;

$D: $size;
$R: $size*0.5;

.roulette{
  
    font-family: 'Work Sans', sans-serif;

    display: block;

    position: absolute;
    width: $size;
    height: $size;
    top: 50%; left: 50%;
    margin-top: -1 * $size * .5;
    margin-left: -1 * $size * .5;
  
  .shadow{
    display: block;
    position: absolute;
    top: 0; left: 0;
    right: 0; bottom: 0;
    border-radius: 50%;
    box-shadow: 0 .3em 0 rgba(0,0,0,.25) inset;
  }
  .markers{
    display: block;
    position: absolute;
    top: -1px; left: -1px;
    right: -1px; bottom: -1px;
    overflow: hidden;
    border-radius: 100%;
    .marker{
      position: absolute;
      width: 0;
      height: 0;
      top: -1*$R; left: $R;
      transform-origin: 0% $D;
      border: 0 solid transparent;
    }
    .triangle{

      width: 0;
      height: 0;
      border-style: solid;
      border-width: 1em 0 1em 1em;
      border-color: transparent transparent transparent #007bff;
      position: absolute;
      border-left-color: #FFF;
      top: 50%;
      left: -1px;
      margin-top: -1em;
      filter: drop-shadow(0 0.25em 0 rgba(0, 0, 0, 0.25));
      
    }
    
  }
  .spinner{

    display: block;
    position: absolute;
    top: 0; left: 0;
    right: 0; bottom: 0;
    border-radius: 50%;
    overflow: hidden;
    transform: rotateZ(0deg);
    /*performance boost*/
    backface-visibility: hidden;
    
    .item{

      position: absolute;
      width: 0;
      height: 0;
      top: -1*$R; left: $R;
      transform-origin: 0% $D;

      border: 0 solid transparent;

      .label{
        display: block;
        position: absolute;
        color: #FFF;
        font-weight: 800;
        top: 0;
        left: 0;
        white-space: nowrap;
        transform-origin: 0 0;
        font-size: .8em;

        i, .text{
          display: inline-block;
          vertical-align: middle;
          line-height: 1;
          font-size: 1em;
          text-indent: 0;
        }
        i{
          margin-right: 0.1em;
        }
        
      }
    }
    
  }
  
  .button{
    width: 10em;
    height: 10em;
    line-height: 10em;
    top: 50%;
    left: 50%;
    margin-left: -5em;
    margin-top: -5em;
    font-weight: 800;
    z-index: 998;
    position: absolute;
    background: #FFF;
    border: none;
    border-radius: 100%;
    color: #999;
    outline: none;
    cursor: pointer;
    user-select: none;
    box-shadow: 0 .4em 0 rgba(0,0,0,.25);;
    text-align: center;
    transition: transform 0.15s;
    transition-timing-function: cubic-bezier(0.68, -0.55, 0.265, 1.55);
  }
  
  .button:hover{
    color: inherit;
  }
  
  .button span{
    font-size: 1.6em;
    letter-spacing: -0.05em;
  }
  
  &.busy .button{
    transform: scale(0.9);
    box-shadow: 0 .15em 0 rgba(0,0,0,.25);
    color: #999;
    cursor: default;
  }
    
}

/* CUSTOM LABELS */
.roulette{
  .spinner{
    
    .item[data-type="quiz"] .label{
      font-size: 1.5em;
    }
    
    .item[data-type="question"] .label{
      font-size: 1.3em;
      font-weight: 600;
    }
    
    .item[data-type="replay"] .label{
      .text{
        font-size: .6em;
        white-space: initial;
        width: 1em;
        text-align: center;
        line-height: 1.2;
      }
      i{
        font-size: 1.5em;
      }
    }
    
    .item[data-type="time"] .label{
      i{
        font-size: 1.5em;
      }
    }
  }
}
</style>
	</head>
<body>
		<div class="container">
		<button onclick="spinner.spin()">RND</button>
		<button onclick="spinner.spin(0)">0</button>
		<button onclick="spinner.spin(1)">1</button>
		<button onclick="spinner.spin(2)">2</button>
		<button onclick="spinner.spin(3)">3</button>
		<button onclick="spinner.spin(4)">4</button>

		<div class="roulette">
		  <div class="spinner"></div>
		  <div class="shadow"></div>
		  <div class="markers">
			<div class="triangle">

			</div>
		  </div>
		  <div class="button">
			<span>SPIN</span>
		  </div>
		</div>
	</div>
<?php
include_once('./include/scripts.php');
?>

<script>
var data = [
  //{ id: '', color: '#3f297e', text: 'ALL IN', ikon: 'invert_colors' },
  { id: '', type: 'allin', color: '#3f297e', text: 'ALL IN', ikon: 'grade' },
  { id: '', type: 'quiz', color: '#1d61ac', text: '25' },
  { id: '', type: 'quiz', color: '#169ed8', text: '50' },
  { id: '', type: 'quiz', color: '#209b6c', text: '100' },
  { id: '', color: '#60b236', text: '200', ikon: 'stars' },
  { id: '', color: '#efe61f', text: '300' },
  { id: '', color: '#f7a416', text: '500' },
  { id: '', type: 'time', color: '#e6471d', text: '+15', ikon: 'timer' },
  { id: '', type: 'question', color: '#dc0936', text: '#@!?' },
  { id: '', color: '#e5177b', text: 'BONUS!' },
  { id: '', color: '#be107f', text: '+100' },
  { id: '', type: 'replay', color: '#881f7e', text: 'PLAY AGAIN', ikon: 'replay' }
];

var RouletteWheel = function(el, items){
  this.$el = $(el);
  this.items = items || [];
  this._bis = false;
  this._angle = 0;
  this._index = 0;
  this.options = {
    angleOffset: -90
  }
}

_.extend(RouletteWheel.prototype, Backbone.Events);

RouletteWheel.prototype.spin = function(_index){
  
  var count = this.items.length;
  var delta = 360/count;
  var index = !isNaN(parseInt(_index))? parseInt(_index) : parseInt(Math.random()*count);
      
  var a = index * delta + ((this._bis)? 1440 : -1440);
  
  //a+=this.options.angleOffset;
  
  this._bis = !this._bis;
  this._angle = a;
  this._index = index;
  
  var $spinner = $(this.$el.find('.spinner'));
  
  var _onAnimationBegin = function(){
    this.$el.addClass('busy');
    this.trigger('spin:start',this);
  }
  
  var _onAnimationComplete = function(){
    this.$el.removeClass('busy');
    this.trigger('spin:end',this);
  }
  
  $spinner
  .velocity('stop')
  .velocity({
    rotateZ: a +'deg'
  },{
    //easing: [20, 7],
    //easing: [200, 20],
    easing: 'easeOutQuint',
    duration: 5000,
    begin: $.proxy(_onAnimationBegin,this),
    complete: $.proxy(_onAnimationComplete,this)
  });
}
  
RouletteWheel.prototype.render = function(){
  
  var $spinner = $(this.$el.find('.spinner'));
  var D = this.$el.width();
  var R = D*.5;

  var count = this.items.length;
  var delta = 360/count;
  
  for(var i=0; i<count; i++){
    
    var item = this.items[i];
    
    var color = item.color;
    var text = item.text;
    var ikon = item.ikon;
    
    var html = [];
    html.push('<div class="item" ');
    html.push('data-index="'+i+'" ');
    html.push('data-type="'+item.type+'" ');
    html.push('>');
    html.push('<span class="label">');
    if(ikon)
      html.push('<i class="material-icons">'+ikon+'</i>');
    html.push('<span class="text">'+text+'</span>');
    html.push('</span>');
    html.push('</div>');
    
    var $item = $(html.join(''));
    
    var borderTopWidth = D + D*0.0025; //0.0025 extra :D
    var deltaInRadians = delta * Math.PI / 180;
    var borderRightWidth = D / ( 1/Math.tan(deltaInRadians) );
    
    var r = delta*(count-i) + this.options.angleOffset - delta*.5;
    
    $item.css({
      borderTopWidth: borderTopWidth,
      borderRightWidth: borderRightWidth,
      transform: 'scale(2) rotate('+ r +'deg)',
      borderTopColor: color
    });
    
    var textHeight = parseInt(((2*Math.PI*R)/count)*.5);
        
    $item.find('.label').css({
      //transform: 'translateX('+ (textHeight) +'px) translateY('+  (-1 * R) +'px) rotateZ('+ (90 + delta*.5) +'deg)',
      transform: 'translateY('+ (D*-.25) +'px) translateX('+  (textHeight*1.03) +'px) rotateZ('+ (90 + delta*.5) +'deg)',
      height: textHeight+'px',
      lineHeight: textHeight+'px',
      textIndent: (R*.1)+'px'
    });
    
    $spinner.append($item);
       
  }
  
  $spinner.css({
    fontSize: parseInt(R*0.06)+'px'
  })
  
  //this.renderMarker();

  
}

RouletteWheel.prototype.renderMarker = function(){
  
  var $markers = $(this.$el.find('.markers'));
  var D = this.$el.width();
  var R = D*.5;

  var count = this.items.length;
  var delta = 360/count;
      
  var borderTopWidth = D + D*0.0025; //0.0025 extra :D
  var deltaInRadians = delta * Math.PI / 180;
  var borderRightWidth = (D / ( 1/Math.tan(deltaInRadians) ));

  var i = 0;  
  var $markerA = $('<div class="marker">');
  var $markerB = $('<div class="marker">');

  var rA = delta*(count-i-1) - delta*.5 + this.options.angleOffset;
  var rB = delta*(count-i+1) - delta*.5 + this.options.angleOffset;
    
  $markerA.css({
    borderTopWidth: borderTopWidth,
    borderRightWidth: borderRightWidth,
    transform: 'scale(2) rotate('+ rA +'deg)',
    borderTopColor: '#FFF'
  });
  $markerB.css({
    borderTopWidth: borderTopWidth,
    borderRightWidth: borderRightWidth,
    transform: 'scale(2) rotate('+ rB +'deg)',
    borderTopColor: '#FFF'
  })
  
  $markers.append($markerA);
  $markers.append($markerB);
  
}

RouletteWheel.prototype.bindEvents = function(){
  this.$el.find('.button').on('click', $.proxy(this.spin,this));
}


var spinner;
$(window).ready(function(){
  spinner = new RouletteWheel($('.roulette'), data);
  spinner.render();
  spinner.bindEvents();
  
  spinner.on('spin:start', function(r){ console.log('spin start!') });
  spinner.on('spin:end', function(r){ 
    console.log('spin end! -->'+ r._index);
    
    var index = $('[data-index]');
    console.log(data);
  });
})	
</script>
</body>
</html>
<?php
include_once('./include/end.php');
?>
