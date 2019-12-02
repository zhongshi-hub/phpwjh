var dates=[];
getDate()
function getDate(){
	for(var i=0;i<12;i++){
		dates.push({});
		dates[i].id=("1"+i).substr(-2);
		dates[i].name=i+1+" 月";
		dates[i].child=[];
		for(var j=0;j<15;j++){
			dates[i].child.push({})
			dates[i].child[j].id=parseInt(dates[i].id)+('0'+(j+1)).substr(-2);
			dates[i].child[j].name=2017+j+" 年";
		}
	}
}

var json=[{
		"id": 0,
		"name": "借记卡",
		"child": [{
				"id": 1,
				"name": "工商银行"
			}, {
				"id": 2,
				"name": "农业银行"
			}, {
				"id": 3,
				"name": "中国银行"
			}, {
				"id": 4,
				"name": "建设银行"
			}, {
				"id": 5,
				"name": "平安银行"
			}, {
				"id": 6,
				"name": "华夏银行"
			}, {
				"id": 7,
				"name": "广大银行"
			}, {
				"id": 8,
				"name": "中信银行"
			}, {
				"id": 9,
				"name": "北京银行"
			}, {
				"id": 10,
				"name": "光发银行"
			}, {
				"id": 11,
				"name": "邮政储蓄"
			}, {
				"id": 12,
				"name": "交通银行"
			}, {
				"id": 13,
				"name": "招商银行"
			}, {
				"id": 14,
				"name": "兴业银行"
			}, {
				"id": 15,
				"name": "民生银行"
			}, {
				"id": 16,
				"name": "深发展银行"
			}]
	}, {
		"id": 1,
		"name": "信用卡",
		"child": [{
				"id": 1,
				"name": "工商银行"
			}, {
				"id": 2,
				"name": "中国银行"
			}, {
				"id": 3,
				"name": "建设银行"
			}, {
				"id": 4,
				"name": "平安银行"
			}, {
				"id": 5,
				"name": "兴业银行"
			}, {
				"id": 6,
				"name": "华夏银行"
			}, {
				"id": 7,
				"name": "光大银行"
			}, {
				"id": 8,
				"name": "民生银行"
			}, {
				"id": 9,
				"name": "招商银行"
			}, {
				"id": 10,
				"name": "广发银行"
			}, {
				"id": 11,
				"name": "浦发银行"
			}, {
				"id": 12,
				"name": "中信银行"
			}, {
				"id": 13,
				"name": "上海银行"
			}, {
				"id": 14,
				"name": "北京银行"
			}, {
				"id": 15,
				"name": "邮政储蓄"
			}, {
				"id": 16,
				"name": "花旗银行"
			}]
	}
];

//level与data个数要相等
//method接口方法:show,hide
//当level:1时，linkpage不能为true
var method=$('.select-value').selectList({
	//层级，决定选择框是几等分
	level:2,
	//Linkpage：false时才有data1,data2
	// data1:level1,
	// data2:level2,
	//Linkpage：true时才有ddataLink
	dataLink:dates,
	Linkpage:true,
	//显示行数
	line:6,
	//显示高度
	// height:40,
	//是否有默认值,默认为false
	idDefault:true,
	//分割字符，默认为' '
	splitStr:'/',
	//标题html
	header:'<div class="selector-header"><a href="javascript:;" class="selector-cancel">取消</a><a href="javascript:;" class="selector-confirm">确定</a></div>',
	afterOne:function(result){
		// console.info(result.target.html())
	},
	afterTwo:function(result){
		//console.info(result.target.html())
	},
	confirm:function(){
		// console.info($('.select-value').data('value1')+'-'+$('.select-value').data('value2'));
	},
	cancel:function(){
		// console.info($('.select-value').data('value1')+'-'+$('.select-value').data('value2'));
	}
})


