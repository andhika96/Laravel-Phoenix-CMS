const EChartJSVue3 = createApp(
{
	data()
	{
		return {
			responseData: '',
			responseStatus: '',
			responseMessage: '',
			EChartJS: '',
			EChartJSID: ''
		}
	},
	methods:
	{
		echartLineBarGradient: function(idSubmit)
		{
			if (document.getElementById(idSubmit) !== null)
			{
				let chartDom = document.getElementById(idSubmit);
				let myChart = echarts.init(chartDom);
				let option;

				const data = [["Jan", 16000000], ["Feb", 29000000], ["March", 40000000], ["April", 35000000], ["Mei", 130000000], ["Jun", 10000000], ["July", 10000000], ["August", 22000000], ["Sept", 12000000], ["Oct", 14000000], ["Nov", 12500000], ["Des", 28000000]];
				const dateList = data.map(function (item) 
				{
					return item[0];
				});

				const valueList = data.map(function (item) 
				{
					return item[1];
				});

				option = 
				{
					// visualMap: [ { show: false, type: 'continuous', seriesIndex: 0, min: 0, max: 2000 } ],
					tooltip: 
					{ 
						trigger: 'axis',
						// formatter: '{c} Jt',
						formatter: function (param)
						{
							param = param[0];

							if (param.value < 1e3) return param.value;
							if (param.value >= 1e3 && param.value < 1e6) return +(param.value / 1e3).toFixed(1)+" Ribu";
							if (param.value >= 1e6 && param.value < 1e9) return +(param.value / 1e6).toFixed(1)+" Juta";
							if (param.value >= 1e9 && param.value < 1e12) return +(param.value / 1e9).toFixed(1)+" Milyar";
							if (param.value >= 1e12) return +(param.value / 1e12).toFixed(1)+" Triliun";
						}
					},
					xAxis: [ { data: dateList, nameTextStyle: { align: 'right' } } ],
					yAxis: 
					[ 
						{ 
							type: "value", 
							axisLine: { show: !1 },
							axisLabel: 
							{
								overflow: "break",
								// formatter: '{value} Jt'
								formatter: function(value)
								{
									if (value < 1e3) return value;
									if (value >= 1e3 && value < 1e6) return +(value / 1e3).toFixed(1) + " Ribu";
									if (value >= 1e6 && value < 1e9) return +(value / 1e6).toFixed(1) + " Jt";
									if (value >= 1e9 && value < 1e12) return +(value / 1e9).toFixed(1) + " M";
									if (value >= 1e12) return +(value / 1e12).toFixed(1) + " T";
								}
							}, 
						} 
					],
					grid: 
					[
						{
							right: "4%",
							left: "4%",
							bottom: "10%",
							top: "10%"
						},
					],
					series: 
					[
						{
							type: 'line',
							showSymbol: false,
							data: valueList
						}
					]
				};

				option && myChart.setOption(option);
			}
		},
		echartSeriesSimpleBar: function(idSubmit)
		{
			if (document.getElementById(idSubmit) !== null)
			{
				let chartDomSeriesSimple = document.getElementById(idSubmit);
				let myChartSeriesSimple = echarts.init(chartDomSeriesSimple);
				let optionSeriesSimple;

				optionSeriesSimple = 
				{
					legend: 
					{
						left: 'auto',
						right: '0'
					},
					tooltip: {},
					dataset: 
					{
						source: 
						[
							['Matcha', 120, 85.8],
							['Milk', 83.1, 73.4],
							['Cheese', 86.4, 65.2],
							['Walnut', 76, 55],
							['Walnut 2', 76, 55]
						]
					},				
					xAxis: { type: 'category', axisTick: { show: !1 }, splitLine: { show: !1 }, max: 4 },
					yAxis: [ { type: "value", axisLine: { show: !1 } } ],
					grid: 
					[
						{
							right: "6%",
							left: "6%",
							bottom: "10%",
							top: "22%"
						},
					],
					// Declare several bar series, each will be mapped
					// to a column of dataset.source by default.
					series: [
						{ name: 'Projected Revenu', type: 'bar', barWidth: 15, itemStyle: { barBorderRadius: [3, 3, 0, 0], borderWidth: 1 } }, 
						{ name: 'Actual Revenue', type: 'bar', barWidth: 15, itemStyle: { barBorderRadius: [3, 3, 0, 0], borderWidth: 1 } }]
				};

				optionSeriesSimple && myChartSeriesSimple.setOption(optionSeriesSimple);
			}
		},
		echartSimpleBarWithBackground: function(idSubmit)
		{
			if (document.getElementById(idSubmit) !== null)
			{
				let chartDomBarWithBackground = document.getElementById(idSubmit);
				let myChartBarWithBackground = echarts.init(chartDomBarWithBackground);
				let optionBarWithBackground;

				optionBarWithBackground = 
				{
					tooltip: 
					{ 
						trigger: 'axis',
					},
					xAxis: 
					{
						type: 'category',
						data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
					},
					yAxis: 
					{
						type: 'value'
					},
					series: 
					[
						{
							data: [120, 200, 150, 80, 70, 110, 130],
							type: 'bar',
							showBackground: true,
							backgroundStyle: 
							{
								color: 'rgba(180, 180, 180, 0.2)'
							}
						}
					]
				};

				optionBarWithBackground && myChartBarWithBackground.setOption(optionBarWithBackground);
			}
		},
		echartDoughnutWithBorderRadius: function(idSubmit)
		{
			if (document.getElementById(idSubmit) !== null)
			{
				let chartDomDoughnutWithBorderRadius = document.getElementById(idSubmit);
				let myChartDoughnutWithBorderRadius = echarts.init(chartDomDoughnutWithBorderRadius);
				let optionDoughnutWithBorderRadius;

				optionDoughnutWithBorderRadius = 
				{
					tooltip: 
					{
						trigger: 'item'
					},
					legend: 
					{
						width: '100%',
						top: '5%',
						left: 'left'
					},
					series: 
					[
						{
							name: 'Access From',
							type: 'pie',
							radius: ['40%', '70%'],
							top: '15%',
							avoidLabelOverlap: false,
							itemStyle: 
							{
								borderRadius: 10,
								borderColor: '#fff',
								borderWidth: 2
							},
							label: 
							{
								show: false,
								position: 'center'
							},
							emphasis: 
							{
								label: 
								{
									show: true,
									fontSize: 18,
									fontWeight: 'bold'
								}
							},
							labelLine: 
							{
								show: false
							},
							data: 
							[
								{ value: 1048, name: 'Search Engine' },
								{ value: 735, name: 'Direct' },
								{ value: 580, name: 'Email' },
								{ value: 484, name: 'Union Ads' },
								{ value: 300, name: 'Video Ads' }
							]
						}
					]
				};

				optionDoughnutWithBorderRadius && myChartDoughnutWithBorderRadius.setOption(optionDoughnutWithBorderRadius);

			}
		},
		vectorMap: function(idSubmit)
		{
			if (document.getElementById(idSubmit) !== null)
			{
				let map = new jsVectorMap(
				{
					selector: "#"+idSubmit,
					map: "world",
					zoomButtons: false,
					hoverOpacity: .7,
					hoverColor: !1,
					regionStyle:
					{
						initial:
						{
							fill: "rgba(145, 166, 189, 0.25)"
						}
					},
					markerStyle:
					{
						initial:
						{
							r: 9,
							fill: "#727cf5",
							"fill-opacity": .9,
							stroke: "#fff",
							"stroke-width": 7,
							"stroke-opacity": .4
						},
						hover:
						{
							stroke: "#fff",
							"fill-opacity": 1,
							"stroke-width": 1.5
						}
					},
					markers: 
					[
						{ name: 'Egypt', coords: [26.8206, 30.8025] },
						{ name: 'United Kingdom', coords: [55.3781, 3.4360] },
						{ name: 'Indonesia', coords: [-6.200000, 106.816666] },
						{ name: 'United States', coords: [37.0902, -95.7129] },
					]
				});
			}	
		}
	},
	mounted()
	{
		this.echartLineBarGradient("echartLineBarGradient_StatsRevenue");

		this.echartSeriesSimpleBar("echartSeriesSimpleBar_ProjectionActual");

		this.echartSimpleBarWithBackground("echartSimpleBarWithBackground_TotalLastOrder");

		this.echartDoughnutWithBorderRadius("echartDoughnutWithBorderRadius_TotalSales");

		this.vectorMap("vectorMap_UserAccessByLocation");
	}
}).mount('#ph-app-echarts');