/* project budget Chart */
// var options = {
// // 	series: [{
// // 		name: "Yearly Bookings",
// // 		data: window.permonthbookingcounts
// // 	}, {
// // 		// name: "Amount Used",
// // 		// data: [85, 65, 75, 38, 85, 35, 62, 40, 40, 64, 50, 89]
// // 	}],
// // 	chart: {
// // 		height: 320,
// // 		type: 'line',
// // 		zoom: {
// // 			enabled: false
// // 		},
// // 		dropShadow: {
// // 			enabled: true,
// // 			enabledOnSeries: undefined,
// // 			top: 5,
// // 			left: 0,
// // 			blur: 3,
// // 			color: '#000',
// // 			opacity: 0.1
// // 		},
// // 	},
// // 	dataLabels: {
// // 		enabled: false
// // 	},
// // 	legend: {
// // 		position: "top",
// // 		horizontalAlign: "center",
// // 		offsetX: -15,
// // 		fontWeight: "bold",
// // 	},
// // 	stroke: {
// // 		curve: 'smooth',
// // 		width: '3',
// // 		dashArray: [0, 5],
// // 	},
// // 	grid: {
// // 		borderColor: '#f2f6f7',
// // 	},
// // 	colors: ["rgb(98, 89, 202)", "rgba(98, 89, 202, 0.3)"],
// // 	yaxis: {
// // 		title: {
// // 			text: '',
// // 			style: {
// // 				color: '#adb5be',
// // 				// fontSize: '14px',
// // 				fontFamily: 'poppins, sans-serif',
// // 				fontWeight: 600,
// // 				cssClass: 'apexcharts-yaxis-label',
// // 			},
// // 		}
// // 	},
// // 	xaxis: {
// // 		type: 'month',
// // 		categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
// // 		axisBorder: {
// // 			show: false,
// // 			color: 'rgba(119, 119, 142, 0.05)',
// // 			offsetX: 0,
// // 			offsetY: 0,
// // 		},
// // 		axisTicks: {
// // 			show: true,
// // 			borderType: 'solid',
// // 			color: 'rgba(119, 119, 142, 0.05)',
// // 			width: 6,
// // 			offsetX: 0,
// // 			offsetY: 0
// // 		},
// // 		labels: {
// // 			rotate: -90
// // 		}
// // 	}
// // };
// document.getElementById('project').innerHTML = ''
// var chart2 = new ApexCharts(document.querySelector("#project"), options);
// chart2.render();

// function project() {
// 	chart2.updateOptions({
// 		colors: ["rgb(" + myVarVal + ")", "rgba(" + myVarVal + ", 0.3)"],
// 	})
// }

/* web design chart */
// Function to get the last 6 months and current month
function getLast6Months() {
    let months = [];
    let date = new Date();
    for (let i = 0; i < 6; i++) {
        // Get month name (e.g., 'January', 'February', etc.)
        let month = date.toLocaleString('default', { month: 'short' });
        months.push(month);
        
        // Go back to the previous month
        date.setMonth(date.getMonth() - 1);
    }
    return months.reverse(); 
}

// Get the categories for the x-axis
let Mname = getLast6Months();
const currentYear = new Date().getFullYear();

const previousYear = currentYear - 1;


// Your options object with dynamically generated categories
var options = {
    series: [{
		name:  previousYear,
		data: window.previous_year_paid_agent_fee_js,

    }, {
        name: currentYear,
        data: window.window.current_year_paid_agent_fee_js,
    }],
    chart: {
        stacked: true,
        type: 'bar',
        height: 175,
    },
    grid: {
        show: false,
        borderColor: '#f2f6f7',
    },
    colors: ["#e9e9e9", "rgb(98, 89, 202)"],                         
    plotOptions: {
        bar: {
            columnWidth: '15%',
            borderRadius: 5,
            borderRadiusApplication: 'end',
            borderRadiusWhenStacked: 'all',
            colors: {
                ranges: [{
                    from: -100,
                    to: -46,
                    color: 'rgb(98, 89, 202)'
                }, {
                    from: -45,
                    to: 0,
                    color: 'rgb(98, 89, 202)'
                }]
            },
        }
    },
    dataLabels: {
        enabled: false,
    },
    legend: {
        show: false,
        position: 'top',
    },
    yaxis: {
        show: false,
        labels: {
            show: false,
        }
    },
    xaxis: {
        show: false,
        type: 'category',
        categories: Mname, // Dynamic categories based on the last 6 months
        axisBorder: {
            show: false,
            color: 'rgba(119, 119, 142, 0.05)',
            offsetX: 0,
            offsetY: 0,
        },
    }
};

// document.getElementById('website-design').innerHTML = ''
// var chart1 = new ApexCharts(document.querySelector("#website-design"), options);
// chart1.render();
// function websiteDesign() {
//     chart1.updateOptions({
//         colors: ["#e9e9e9","rgb(" + myVarVal + ")"],
//     })
// }
document.getElementById('websitedesign').innerHTML = ''
var chart1 = new ApexCharts(document.querySelector("#websitedesign"), options);
chart1.render();

function websitedesign() {
	chart1.updateOptions({
		colors: ["rgb(" + myVarVal + ")", "rgba(" + myVarVal + ", 0.3)"],
	})
}
/* web design chart */


/* on going Chart */
var options = {
	series: [, 250],
	labels: ["Bitcoin", "Ethereum"],
	chart: {
	  height: 73,
	  width: 50,
		type: 'donut',
	},
	dataLabels: {
		enabled: false,
	},
  
	legend: {
		show: false,
	},
	stroke: {
		show: true,
		curve: 'smooth',
		lineCap: 'round',
		colors: "#fff",
		width: 0,
		dashArray: 0,
	},
	plotOptions: {
  
		pie: {
			expandOnClick: false,
			donut: {
				size: '75%',
				background: 'transparent',
				labels: {
					show: false,
					name: {
						show: true,
						fontSize: '20px',
						color: '#495057',
						offsetY: -4
					},
					value: {
						show: true,
						fontSize: '18px',
						color: undefined,
						offsetY: 8,
						formatter: function (val) {
							return val + "%"
						}
					},
					total: {
						show: true,
						showAlways: true,
						label: 'Total',
						fontSize: '22px',
						fontWeight: 600,
						color: '#495057',
					}
  
				}
			}
		}
	},
	colors: ["rgb(98, 89, 202)", "rgba(98, 89, 202, 0.2)"],
};
document.querySelector('#ongoingprojects').innerHTML = ''
var chart = new ApexCharts(document.querySelector("#ongoingprojects"), options);
chart.render();

function ongoingprojects() {
	chart.updateOptions({
		colors: ["rgb(" + myVarVal + "),", "rgba(" + myVarVal + ", 0.3)"],
	})
}


////////////
var options = {
	series: [1754, 544],
	labels: ["Bitcoin", "Ethereum"],
	chart: {
	  height: 73,
	  width: 50,
		type: 'donut',
	},
	dataLabels: {
		enabled: false,
	},
  
	legend: {
		show: false,
	},
	stroke: {
		show: true,
		curve: 'smooth',
		lineCap: 'round',
		colors: "#fff",
		width: 0,
		dashArray: 0,
	},
	plotOptions: {
  
		pie: {
			expandOnClick: false,
			donut: {
				size: '75%',
				background: 'transparent',
				labels: {
					show: false,
					name: {
						show: true,
						fontSize: '20px',
						color: '#495057',
						offsetY: -4
					},
					value: {
						show: true,
						fontSize: '18px',
						color: undefined,
						offsetY: 8,
						formatter: function (val) {
							return val + "%"
						}
					},
					total: {
						show: true,
						showAlways: true,
						label: 'Total',
						fontSize: '22px',
						fontWeight: 600,
						color: '#495057',
					}
  
				}
			}
		}
	},
	colors: ["rgb(98, 89, 202)", "rgba(98, 89, 202, 0.2)"],
};

document.querySelector('#ongoingprojects2').innerHTML = ''
var chart4 = new ApexCharts(document.querySelector("#ongoingprojects2"), options);
chart4.render();

function ongoingprojects2() {
	chart4.updateOptions({
		colors: ["rgb(" + myVarVal + ")", "rgba(" + myVarVal + ", 0.3)"],
	})
}
/* on going Chart */

/* today task chart */
var options = {
	chart: {
	  height: 100,
	  type: "radialBar"
	},
	
	series: [window.paidCommissionPercentage],
	
	colors: ["rgb(98, 89, 202)"],

	states: {
		normal: {
			filter: {
				type: 'none',
			}
		},
		hover: {
			filter: {
				type: 'none',
			}
		},
		active: {
			filter: {
				type: 'none',
			}
		},
	},
	
	plotOptions: {
	  radialBar: {
		hollow: {
		  size: "60%"
		},
	   
		dataLabels: {
		  showOn: "always",
		  name: {
			offsetY: -10,
			show: false,
			color: "#888",
			fontSize: "13px"
		  },
		  value: {
			offsetY: 5,
			color: "#111",
			fontSize: "18px",
			fontWeight: 'bold',
			show: true
		  }
		}
	  }
	},

	grid: {
		padding: {
			top: -20,
			right: -25,
			bottom: -20,
			left: -25
		},
	},
  
	stroke: {
	  lineCap: "round",
	},
	labels: [""]
};
// document.querySelector("#today-task").innerHTML = " ";
// var chart = new ApexCharts(document.querySelector("#today-task"), options);
// chart.render();

document.querySelector('#todaytask').innerHTML = ''
var chart3 = new ApexCharts(document.querySelector("#todaytask"), options);
chart3.render();

function todaytask() {
	chart3.updateOptions({
		colors: ["rgb(" + myVarVal + ")"],
	})
}
/* today task chart */