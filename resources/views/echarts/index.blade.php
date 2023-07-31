<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>ECharts</title>
    <!-- 引入刚刚下载的 ECharts 文件 -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('echarts/js/echarts.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/jquery-3.7.0.min.js') }}"></script>
    <style>
        #echarts-box {
            background: #ebeeed;
            margin: 10px;
        }

        #echarts-box .form-control {
            width: 200px;
        }

        #echarts-box .row-box {
            margin: 10px;
            font-weight: bold;
        }

        #table-box {
            background: #ebeeed;
            margin: 10px;
        }

        #table-box .form-control {
            width: 200px;
        }
    </style>
</head>
<body>
{{--走势图搜索--}}
<div id="echarts-box">
    <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">网站选择</span>
        <select id="selected-echarts" class="form-control">
            <option value="0">选择网站</option>
        </select>
    </div>

    <!-- 为 ECharts 准备一个定义了宽高的 DOM -->
    <div id="main" style="width: 100%;height:400px;"></div>
    <div class="row row-box">
        <div class="col-md-1">总蜘蛛</div>
        <div class="col-md-1">百度:100</div>
        <div class="col-md-1">百度:100</div>
        <div class="col-md-1">百度:100</div>
        <div class="col-md-1">百度:100</div>
        <div class="col-md-1">百度:100</div>
        <div class="col-md-1">百度:100</div>
    </div>
</div>

{{--表格搜索--}}
<div id="table-box">
    <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">蜘蛛选择</span>
        <select class="form-control">
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
        </select>
    </div>
    <table class="table table-hover">
        <tr style="background: #dddddd">
            <th>日期</th>
            <th>网站</th>
            <th>来源</th>
            <th>设备</th>
            <th>IP</th>
        </tr>
        <tr>
            <td>2023-1-1</td>
            <td>www.baidu.com</td>
            <td>百度</td>
            <td>PC</td>
            <td>127.0.0.1</td>
        </tr>
        <tr>
            <td>2023-1-1</td>
            <td>www.baidu.com</td>
            <td>百度</td>
            <td>PC</td>
            <td>127.0.0.1</td>
        </tr>
        <tr>
            <td>2023-1-1</td>
            <td>www.baidu.com</td>
            <td>百度</td>
            <td>PC</td>
            <td>127.0.0.1</td>
        </tr>
        <tr>
            <td>2023-1-1</td>
            <td>www.baidu.com</td>
            <td>百度</td>
            <td>PC</td>
            <td>127.0.0.1</td>
        </tr>
        <tr>
            <td>2023-1-1</td>
            <td>www.baidu.com</td>
            <td>百度</td>
            <td>PC</td>
            <td>127.0.0.1</td>
        </tr>


    </table>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li>
                <a href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li><a href="#">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">4</a></li>
            <li><a href="#">5</a></li>
            <li>
                <a href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
<script type="text/javascript">
    // 处理网站选择搜索
    $.post('/api/get_all_host', {}, function (data) {
        let html = '';
        for (let i = 0; i < data.length; i++) {
            html += '<option value="' + data[i].host + '">' + data[i].host + '</option>';
        }
        $('#selected-echarts').append(html);
    }, "JSON");

    // 查询网站
    $('#selected-echarts').change(function () {
        let host = $(this).val();
        if (host === 0) {
            return;
        }
        $.post('/api/get_charts_data', {host: host}, function (data) {
            console.log(data);
        }, "JSON");
    });
    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('main'));
    var selected_array = ['百度', '神马', '谷歌', '搜狗', '优酷'];
    // 指定图表的配置项和数据
    var option = option = {
        title: {
            text: '蜘蛛统计'
        },
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data: selected_array
        },
        selected: {
            "百度": true,
            "神马": true,
            "谷歌": true,
            "搜狗": true,
            "优酷": true,
        },
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
            data: ['2023-7-25', '2023-7-26', '2023-7-27', '2023-7-28', '2023-7-29', '2023-7-30', '2023-7-31']
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name: '百度',
                type: 'line',
                stack: 'Total',
                data: [120, 132, 101, 134, 90, 230, 210]
            },
            {
                name: '神马',
                type: 'line',
                stack: 'Total',
                data: [220, 182, 191, 234, 290, 330, 310]
            },
            {
                name: '谷歌',
                type: 'line',
                stack: 'Total',
                data: [150, 232, 201, 154, 190, 330, 410]
            },
            {
                name: '搜狗',
                type: 'line',
                stack: 'Total',
                data: [320, 332, 301, 334, 390, 330, 320]
            },
            {
                name: '优酷',
                type: 'line',
                stack: 'Total',
                data: [820, 932, 901, 934, 1290, 1330, 1320]
            }
        ]
    };

    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
</script>
</body>
</html>
