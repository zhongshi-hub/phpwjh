var jsonData=[{
		"id": 0,
		"name": "借记卡",
		"child": [{
				"id": "icbc",
				"name": "工商银行"
			}, {
				"id": "abc",
				"name": "农业银行"
			}, {
				"id": "boc",
				"name": "中国银行"
			}, {
				"id": "ccb",
				"name": "建设银行"
			}, {
				"id": "pingan",
				"name": "平安银行"
			}, {
				"id": "hb",
				"name": "华夏银行"
			}, {
				"id": "ceb",
				"name": "广大银行"
			}, {
				"id": "citic",
				"name": "中信银行"
			}, {
				"id": "bob",
				"name": "北京银行"
			}, {
				"id": "cgb",
				"name": "广发银行"
			}, {
				"id": "psbc",
				"name": "邮政储蓄"
			}, {
				"id": "bocm",
				"name": "交通银行"
			}, {
				"id": "cmb",
				"name": "招商银行"
			}, {
				"id": "cib",
				"name": "兴业银行"
			}, {
				"id": "cmbc",
				"name": "民生银行"
			}, {
				"id": "sdb",
				"name": "深发展银行"
			}]
	}, {
		"id": 2,
		"name": "信用卡",
		"child": [{
				"id": "icbc",
				"name": "工商银行"
			}, {
				"id": "boc",
				"name": "中国银行"
			}, {
				"id": "ccb",
				"name": "建设银行"
			}, {
				"id": "pingan",
				"name": "平安银行"
			}, {
				"id": "cib",
				"name": "兴业银行"
			}, {
				"id": "hxb",
				"name": "华夏银行"
			}, {
				"id": "ceb",
				"name": "光大银行"
			}, {
				"id": "cmbc",
				"name": "民生银行"
			}, {
				"id": "cmb",
				"name": "招商银行"
			}, {
				"id": "gdb",
				"name": "广发银行"
			}, {
				"id": "spdb",
				"name": "浦发银行"
			}, {
				"id": "ccb",
				"name": "中信银行"
			}, {
				"id": "bos",
				"name": "上海银行"
			}, {
				"id": "bob",
				"name": "北京银行"
			}, {
				"id": "psbc",
				"name": "邮政储蓄"
			}, {
				"id": "citibank",
				"name": "花旗银行"
			}]
	}
];

var json=[{
		"id": 0,
		"name": "话费网游",
	}, {
		"id": 1,
		"name": "男女服装"
	}, {
		"id": 2,
		"name": "鞋包配饰"
	}, {
		"id": 3,
		"name": "美容美妆"
	}, {
		"id": 4,
		"name": "运动户外"
	}, {
		"id": 5,
		"name": "数码家电"
	}, {
		"id": 6,
		"name": "家居日用"
	}, {
		"id": 7,
		"name": "食品保健"
	}, {
		"id": 8,
		"name": "母婴用品"
	},{
		"id": 9,
		"name": "车品图书"
	}, {
		"id": 10,
		"name": "演出旅游"
	}, {
		"id": 11,
		"name": "全民服务"
	},{
		"id": 12,
		"name": "中介服务"
	}, {
		"id": 13,
		"name": "生活电器"
	}, {
		"id": 14,
		"name": "实名票务"
	}, {
		"id": 15,
		"name": "其他"
	}
];

//level与data个数要相等
//method接口方法:show,hide
//当level:1时，linkpage不能为true
var method=$('.select-bank').selectList({
	//层级，决定选择框是几等分
	level:2,
	//Linkpage：false时才有data1,data2
	// data1:level1,
	// data2:level2,
	//Linkpage：true时才有ddataLink
	dataLink:jsonData,
	Linkpage:true,
	//显示行数
	line:6,
	//显示高度
	// height:40,
	//是否有默认值,默认为false
	idDefault:true,
	//分割字符，默认为' '
	splitStr:'-',
	//标题html
	header:'<div class="selector-header"><a href="javascript:;" class="selector-cancel">取消</a><a href="javascript:;" class="selector-confirm">确定</a></div>',
	afterOne:function(result){
		// console.info(result.target.html())
	},
	afterTwo:function(result){
		//console.info(result.target.html())
	},
	confirm:function(){
		$("#cardType").val($('.select-bank').data('value1'));
		$("#bankCode").val($('.select-bank').data('value2'));
	},
	cancel:function(){
		// console.info($('.select-value').data('value1')+'-'+$('.select-value').data('value2'));
	}
})



