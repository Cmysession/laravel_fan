<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>统计图</title>
    <!-- 引入刚刚下载的 ECharts 文件 -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('bootstrap/js/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('echarts/js/echarts.min.js') }}"></script>

    <style>
        #echarts-box {
            background: #ebeeed;
            margin: 10px;
        }

        #echarts-box .form-control {
            width: 200px;
        }

        #table-box,
        #container-box {
            background: #ebeeed;
            margin: 10px;
        }

        #table-box .form-control,
        #container-box .form-control {
            width: 200px;
        }

        #table-box .btn {
            margin: 10px;
        }

        #go_top {
            height: 70px;
            width: 70px;
            position: fixed;
            right: 20px;
            bottom: 20px;
            border-radius: 100px;
        }

        .row {
            width: 100%;
            margin: 10px;
        }

        .row .col-md-4 {
            margin: 10px;
            width: 20%;
            font-size: 30px;
            font-weight: bold;
        }

        .row .col-md-4 span {
            font-weight: 300;
            font-size: 25px;
            margin-left: 5px;
        }
    </style>
</head>

<body>
    {{-- 走势图搜索 --}}
    <div id="container-box">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">时间选择</span>
            <input type="date" class="form-control" id="search-date">
        </div>
        <!-- 为 ECharts 准备一个定义了宽高的 DOM -->
        <div id="chart-container" style="position: relative;width: 100%;height:400px;overflow: hidden;"></div>
    </div>

    {{-- 走势图搜索 --}}
    <div id="echarts-box">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">网站选择</span>
            <select id="selected-echarts" class="form-control">
                <option value="0">选择网站</option>
            </select>
        </div>

        <!-- 为 ECharts 准备一个定义了宽高的 DOM -->
        <div id="main" style="width: 100%;height:400px;"></div>
    </div>


    {{-- 表格搜索 --}}
    <div id="table-box">

        <div class="logs-box"></div>
        <div class="row">
            <div class="col-md-4">百度:<span id="bd">0</span></div>
            <div class="col-md-4">搜狗:<span id="sg">0</span></div>
            <div class="col-md-4">360:<span id="s6">0</span></div>
            <div class="col-md-4">神马:<span id="sm">0</span></div>
        </div>
        <table class="table table-hover">
            <tr style="background: #dddddd">
                <th>日期</th>
                <th>网站</th>
                <th>来源</th>
                <th>设备</th>
                <th>IP</th>
                <tbody id="logs-box-td"></tbody>
            </tr>
        </table>
    </div>

    <button id="go_top" type="button" class="btn btn-danger">顶部</button>
    <script type="text/javascript">
        // 处理网站选择搜索
        $.get('/api/get_all_host?' + new Date(), {}, function(data) {
            let html = '';
            for (let i = 0; i < data.length; i++) {
                html += '<option value="' + data[i].host + '">' + data[i].host + '</option>';
            }
            $('#selected-echarts').append(html);
        }, "JSON");

        var now = new Date();
        var yy = now.getFullYear(); //年
        var mm = now.getMonth() + 1; //月
        var dd = now.getDate(); //日
        var clock = yy + "-";
        if (mm < 10) clock += "0";
        clock += mm + "-";
        if (dd < 10) clock += "0";
        var date = clock += dd;
        $('#search-date').val(date);

        // 获取柱状
        function day_charts(date) {
            $.get('/api/get_day_charts?' + new Date(), {
                date: date
            }, function(data) {
                //柱状图
                var dom = document.getElementById('chart-container');
                var myChart = echarts.init(dom, null, {
                    renderer: 'canvas',
                    useDirtyRect: false
                });
                var app = {};

                var option;

                const posList = [
                    'left',
                    'right',
                    'top',
                    'bottom',
                    'inside',
                    'insideTop',
                    'insideLeft',
                    'insideRight',
                    'insideBottom',
                    'insideTopLeft',
                    'insideTopRight',
                    'insideBottomLeft',
                    'insideBottomRight'
                ];
                app.configParameters = {
                    rotate: {
                        min: -90,
                        max: 90
                    },
                    align: {
                        options: {
                            left: 'left',
                            center: 'center',
                            right: 'right'
                        }
                    },
                    verticalAlign: {
                        options: {
                            top: 'top',
                            middle: 'middle',
                            bottom: 'bottom'
                        }
                    },
                    position: {
                        options: posList.reduce(function(map, pos) {
                            map[pos] = pos;
                            return map;
                        }, {})
                    },
                    distance: {
                        min: 0,
                        max: 100
                    }
                };
                app.config = {
                    rotate: 90,
                    align: 'left',
                    verticalAlign: 'middle',
                    position: 'insideBottom',
                    distance: 15,
                    onChange: function() {
                        const labelOption = {
                            rotate: app.config.rotate,
                            align: app.config.align,
                            verticalAlign: app.config.verticalAlign,
                            position: app.config.position,
                            distance: app.config.distance
                        };
                        myChart.setOption({
                            series: [{
                                    label: labelOption
                                },
                                {
                                    label: labelOption
                                },
                                {
                                    label: labelOption
                                },
                                {
                                    label: labelOption
                                }
                            ]
                        });
                    }
                };
                const labelOption = {
                    show: true,
                    position: app.config.position,
                    distance: app.config.distance,
                    align: app.config.align,
                    verticalAlign: app.config.verticalAlign,
                    rotate: app.config.rotate,
                    formatter: '{c}  {name|{a}}',
                    fontSize: 16,
                    rich: {
                        name: {}
                    }
                };
                option = {
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        }
                    },
                    legend: {
                        data: data.legend_data
                    },
                    toolbox: {
                        show: true,
                        orient: 'vertical',
                        left: 'right',
                        top: 'center',
                        feature: {
                            mark: {
                                show: true
                            },
                            dataView: {
                                show: true,
                                readOnly: false
                            },
                            magicType: {
                                show: true,
                                type: ['line', 'bar', 'stack']
                            },
                            restore: {
                                show: true
                            },
                            saveAsImage: {
                                show: true
                            }
                        }
                    },
                    xAxis: [{
                        type: 'category',
                        axisTick: {
                            show: false
                        },
                        data: data.xAxis_data
                    }],
                    yAxis: [{
                        type: 'value'
                    }],
                    series: data.series
                };

                if (option && typeof option === 'object') {
                    myChart.setOption(option);
                }

                window.addEventListener('resize', myChart.resize);
            }, "JSON");
        }

        day_charts(date);
        $('#search-date').change(function() {
            let date = $(this).val();
            day_charts(date);
        });

        function show_charts(host) {
            $.get('/api/get_charts_data?' + new Date(), {
                host: host
            }, function(data) {
                var myChart = echarts.init(document.getElementById('main'));
                // 基于准备好的dom，初始化echarts实例
                var selected_array = data.selected_array;
                // 指定图表的配置项和数据
                var option = {
                    title: {
                        text: '蜘蛛统计'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: selected_array
                    },
                    selected: data.selected,
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    toolbox: {
                        feature: {
                            saveAsImage: {}
                        }
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: data.xAxis_data
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: data.series
                };
                // 使用刚指定的配置项和数据显示图表。
                myChart.setOption(option);
            }, "JSON");
        }

        function show_table(host, log) {
            //获取表格数据
            $.get('/api/get_table_list?' + new Date(), {
                host: host,
                log: log
            }, function(data) {
                let date_list = '';
                for (var i = 0; i < data.logs.length; i++) {
                    date_list += '<button type="button" class="btn ' + (log === data.logs[i] ? 'btn-success' :
                        'btn-default') + ' btn-sm">' + data.logs[i] + '</button>';
                }
                $('.logs-box').html(date_list);
                $('#bd').text(data.total.bd);
                $('#sg').text(data.total.sg);
                $('#s6').text(data.total.s6);
                $('#sm').text(data.total.sm);
                let table_list = '';
                for (var i = 0; i < data.data.length; i++) {
                    table_list += "<tr><td>" + data.data[i].date + "</td><td>" +
                        data.data[i].url + "</td><td>" +
                        data.data[i].bot + "</td><td>" +
                        data.data[i].device + "</td><td>" +
                        data.data[i].ip + "</td></tr>";
                }
                $('#logs-box-td').html(table_list);
            }, "JSON");
        }

        // 查询网站
        $('#selected-echarts').change(function() {
            let host = $(this).val();
            if (host === 0) {
                return;
            }
            show_charts(host);
            show_table(host, 'spider-' + date + '.log');
            $('.logs-box').on('click', '.btn', function() {
                show_table(host, $(this)[0].innerText);
            })
        });


        go_top.onclick = function() {
            document.body.scrollTop = document.documentElement.scrollTop = 0;
        }
    </script>
</body>

</html>
