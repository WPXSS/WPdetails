<?php
function wp_show_stats_posts() {
    global $wpdb;
    $totalPosts = wp_count_posts();
    $totalPostsArray = (array)$totalPosts;
    unset($totalPostsArray['auto-draft']);
    unset($totalPostsArray['inherit']);
    $countPosts = array_sum($totalPostsArray);
	$years = $wpdb->get_results("SELECT YEAR(post_date) AS year FROM " . $wpdb->prefix . "posts 
            WHERE post_type = 'post' AND post_status = 'publish' 
            GROUP BY year DESC");
    $yearWiseArray = array();
    $monthsArray = array();
    foreach($years as $k => $year){
	$yearWisePosts = $wpdb->get_results("
            SELECT YEAR(post_date) as post_year, COUNT(ID) as post_count 
                FROM " . $wpdb->prefix . "posts
                WHERE YEAR(post_date) =  '" . $year->year . "' AND post_type = 'post' 
                GROUP BY post_year
                ORDER BY post_date ASC"
        );
        if(!empty($yearWisePosts[0]->post_year)){
            $yearWiseArray[$yearWisePosts[0]->post_year] = $yearWisePosts[0]->post_count;
        }
        
	$monthWisePosts = $wpdb->get_results("
            SELECT MONTH(post_date) as post_month, COUNT(ID) as post_count 
                FROM " . $wpdb->prefix . "posts
                WHERE YEAR(post_date) =  '" . $year->year . "' AND post_type = 'post'
                GROUP BY post_month
                ORDER BY post_date ASC"
            );
        
        foreach($monthWisePosts as $mk => $post){
            $monthWiseArray[$year->year][$post->post_month] = $post->post_count;
        }
    }
   foreach($monthWiseArray as $y => $arr){
       $test_arr = array();
       for($i = 1; $i<=12; $i++){
           $test_arr[$i] = isset($arr[$i]) ? $arr[$i] : 0;
       }
       $monthsArray[$y] = implode(",", $test_arr);
   }
    
    ?>


<?php 
    $getcolor = wpd_dashboard_widget_color();

?>


<?php if(sizeof($monthsArray) > 0){ 

$x_months = "['".__('Jan','wpdlang')."', '".__('Feb','wpdlang')."', '".__('Mar','wpdlang')."','".__('Apr','wpdlang')."', '".__('May','wpdlang')."', '".__('Jun','wpdlang')."', '".__('Jul','wpdlang')."', '".__('Aug','wpdlang')."', '".__('Sep','wpdlang')."', '".__('Oct','wpdlang')."', '".__('Nov','wpdlang')."', '".__('Dec','wpdlang')."']";

$monthsArraycolors = array($getcolor[0],$getcolor[1],$getcolor[2],$getcolor[3],$getcolor[4],$getcolor[5]);

$legendsyear = "";
foreach ($monthsArray as $yeartitle => $monthdata) { $legendsyear .= "'".$yeartitle."', "; }
$legendsyear = substr($legendsyear,0,-2);

?>    
            <div class="chartBox"><?php // print_r($monthsArray); ?>
                 <div id="wpd_posts_byMonthYear" style='height:180px;'></div>
            </div>


<script type="text/javascript">
        var myChart8 = echarts.init(document.getElementById('wpd_posts_byMonthYear')); 
        
        var option_month = {
                grid: {
                    zlevel: 0,
                    x: 30,
                    x2: 50,
                    y: 20,
                    y2: 20,
                    borderWidth: 0,
                    backgroundColor: 'rgba(0,0,0,0)',
                    borderColor: 'rgba(0,0,0,0)',
                },

                // Add tooltip
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { 
                        type: 'shadow', // line|shadow
                        lineStyle:{color: 'rgba(0,0,0,.5)', width: 1},
                        shadowStyle:{color: 'rgba(0,0,0,.1)'}
                      }
                },

                // Add legend
                legend: {
                    data: [<?php echo $legendsyear; ?>]
                },
                toolbox: {
                  orient: 'vertical',
                    show : true,
                    showTitle: true,
                    color : ['#bdbdbd','#bdbdbd','#bdbdbd','#bdbdbd'],
                    itemSize: 13,
                    itemGap: 10,
                },

                // Enable drag recalculate
                calculable: true,

                // Horizontal axis
                xAxis: [{
                    type: 'category',
                    boundaryGap: false,
                    data: <?php echo $x_months; ?>,
                    axisLine: {
                        show: true,
                        onZero: true,
                        lineStyle: {
                            color: '#757575',
                            type: 'solid',
                            width: '2',
                            shadowColor: 'rgba(0,0,0,0)',
                            shadowBlur: 5,
                            shadowOffsetX: 3,
                            shadowOffsetY: 3,
                        },
                    },                    
                    axisTick: {
                        show: false,
                    },
                    splitLine: {
                          show: false,
                          lineStyle: {
                              color: '#fff',
                              type: 'solid',
                              width: 0,
                              shadowColor: 'rgba(0,0,0,0)',
                        },
                    },
                }],

                // Vertical axis
                yAxis: [{
                    type: 'value',
                    splitLine: {
                          show: false,
                          lineStyle: {
                              color: 'fff',
                              type: 'solid',
                              width: 0,
                              shadowColor: 'rgba(0,0,0,0)',
                        },
                    },
                    axisLabel: {
                        show: false,
                    },                    
                    axisTick: {
                        show: false,
                    },                    
                    axisLine: {
                        show: false,
                        onZero: true,
                        lineStyle: {
                            color: '#ff0000',
                            type: 'solid',
                            width: '0',
                            shadowColor: 'rgba(0,0,0,0)',
                            shadowBlur: 5,
                            shadowOffsetX: 3,
                            shadowOffsetY: 3,
                        },
                    },


                }],

                series: [
                <?php 
                $monthyear = 0;
                foreach ($monthsArray as $yeartitle => $monthdata) { 
                    
                ?>
                    {
                        name: '<?php echo $yeartitle; ?>',
                        type: 'bar',
                        smooth: true,
                        symbol:'none',
                        symbolSize:2,
                        showAllSymbol: true,
                        itemStyle: {
                          normal: {
                            color:'<?php echo $monthsArraycolors[$monthyear]; ?>', 
                            borderWidth:2, borderColor:'<?php echo $monthsArraycolors[$monthyear]; ?>', 
                            areaStyle: {color:'<?php echo $monthsArraycolors[$monthyear]; ?>', type: 'default'}
                          }
                        },

                        data: [<?php echo $monthdata; ?>]
                    },
                <?php 
                    $monthyear++; 
                } ?>
                    ]
            };

        // Load data into the ECharts instance 
        myChart8.setOption(option_month); 
                    jQuery(window).on('resize', function(){
                      myChart8.resize();
                    });
        
    </script>



<?php } ?>




<?php 

if(sizeof($yearWiseArray) > 0){

        $x_years = ""; $y_total = "";

         foreach ($yearWiseArray as $key => $value) {
            $x_years .= "'".$key."', ";
            $y_total .= $value.", ";
         }

         $x_years = substr($x_years,0,-2);
         $x_years = "[".$x_years."]";

         $y_total = substr($y_total,0,-2);
         $y_total = "[".$y_total."]";

         //echo "". $x_years." ".$y_total; ?>



<?php } ?>





        <?php if($countPosts > 0){ 
            
            $data_str = "";
            $data_obj = "";
            //if(isset($usersCount['avail_roles']) && sizeof($usersCount['avail_roles']) > 0){
                foreach ($totalPostsArray as $key => $value) {
                    $data_str .= "'".ucfirst($key)."', ";

                    if($value == '0'){ $value = "'-'";}
                     $data_obj .= "{value: ".$value.",  name:'".ucfirst($key)."'}, ";
                }

                 $data_str = substr($data_str,0,-2);
                 $data_str = "[".$data_str."]";

                 $data_obj = substr($data_obj,0,-2);
                 $data_obj = "[".$data_obj."]";

           // }
        ?>
            <script type="text/javascript">
              // Initialize after dom ready
              var myChart10 = echarts.init(document.getElementById('totalPosts_wiseChart')); 
                    
              var option = {
                color: ['<?php echo $getcolor[0]; ?>','<?php echo $getcolor[1]; ?>','<?php echo $getcolor[2]; ?>','<?php echo $getcolor[3]; ?>','<?php echo $getcolor[4]; ?>','<?php echo $getcolor[5]; ?>'],

                    tooltip : {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                            legend: {
                                x: 'left',
                                orient:'vertical',
                                padding: 0,
                                data:<?php echo $data_str; ?>
                            },
                    toolbox: {
                        show : true,
                        color : ['#bdbdbd','#bdbdbd','#bdbdbd','#bdbdbd'],
                    itemSize: 13,
                    itemGap: 10,
                    },
                    calculable : true,
                    series : [
                        {
                            name:'<?php echo __("Post Count","wpdlang"); ?>',
                            type:'pie',
                            radius : [20, '80%'],
                            roseType : 'radius',
                            center: ['50%', '45%'],
                            width: '50%',       // for funnel
                            max: 40,            // for funnel
                            itemStyle : {
                                   normal : { label : { show : true }, labelLine : { show : true } },
                                   emphasis : { label : { show : false }, labelLine : {show : false} }
                             },
                            data:<?php echo $data_obj; ?>
                        }
                    ]};

                    // Load data into the ECharts instance 
                    myChart10.setOption(option); 
                    jQuery(window).on('resize', function(){
                      myChart10.resize();
                    });
                    
                </script>

        <?php } ?>
        





<?php } ?>