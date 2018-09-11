<?php
if (!defined('_GNUBOARD_')) exit;

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('debug', GML_PLUGIN_PATH.'/debugbar/'.GML_LANG_DIR) );

add_stylesheet('<link rel="stylesheet" href="'.GML_PLUGIN_URL.'/debugbar/style.css">', 0);
?>
<style>
<?php if (defined('GML_IS_ADMIN') && GML_IS_ADMIN){ ?>
.debug_bar_wrap{z-index:991001}
<?php } ?>
</style>
<div class="debug_bar_wrap">
    <div class="debug_bar_text_group">
        <div class="debug_bar_btn_group"><button class="view_debug_bar debug_button"><?php e__('Debug'); ?></button></div>
        <div class="debug_bar_text">
            <?php echo __('PHP RUN TIME :').' '.$php_run_time.' | '.__('MEMORY USAGE :').' '.number_format($memory_usage).' bytes'; ?>
        </div>
    </div>
    <div class="debug_bar_content">
        <div class="content_inner">

            <div class="debugbar_close_btn_el"><button class="debugbar_close_btn btn"><?php e__('Close'); ?></button></div>
            <div id="debugbar">
                <ul class="debugbar_tab">
                    <li class="debug_tab active" data-tab="executed_query"><a href="#debug_executed_query"><?php e__('SQL Query'); ?></a></li>
                    <li class="debug_tab" data-tab="language_info"><a href="#debug_language_info"><?php e__('Language Info'); ?></a></li>
                    <li class="debug_tab" data-tab="hook_info"><a href="#debug_hook_info"><?php e__('Hook Info'); ?></a></li>
                </ul>
            </div>

            <div id="debug_executed_query" class="inner_debug">
                <h3 class="query_top">
                    <?php e__('Total Queries :'); ?><span><?php echo isset($gml_debug['sql']) ? count($gml_debug['sql']) : 0; ?></span>
                </h3>

                <div class="sql_query_list">
                <table class="debug_table">
                    <caption>
                    <?php e__('Query List'); ?>
                    </caption>
                    <thead>
                        <tr>
                            <th scope="col"><?php e__('RUN ORDER'); ?></th>
                            <th scope="col"><?php e__('QUERY'); ?></th>
                            <th scope="col"><?php e__('RUN TIME'); ?></th>
                        </tr>
                    </thead>
                <tbody>
                <?php
                foreach((array) $gml_debug['sql'] as $key=>$query){

                if( empty($query) ) continue;

                $executed_time = $gml_debug['end_time'][$key] - $gml_debug['start_time'][$key];
                $show_excuted_time = number_format((float)$executed_time * 1000, 2, '.', '');
                ?>
                <tr>
                    <td scope="row" data-label="<?php e__('RUN ORDER'); ?>"><?php echo $key; ?></td>
                    <td class="left" data-label="<?php e__('QUERY'); ?>"><?php echo $query; ?></td>
                    <td data-label="<?php e__('RUN TIME'); ?>"><?php echo $show_excuted_time.' ms'; ?></td>
                </tr>
                <?php } ?>

                </tbody>

                </table>
                </div>
            </div>

            <div id="debug_language_info" class="inner_debug">
                <div class="lang_domain_list">
                
                    <div class="debug_table_wrap">
                        <table class="debug_table">
                        <caption>
                        <?php e__('Query List'); ?>
                        </caption>
                        <thead>
                        <tr>
                            <th scope="col"><?php e__('DOMAIN'); ?></th>
                            <th scope="col"><?php e__('FILE_PATH'); ?></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        foreach((array) $l10n as $domain=>$datas){

                        if( empty($datas) ) continue;

                        ?>
                        <tr>
                            <td scope="row" data-label="<?php e__('DOMAIN'); ?>"><?php echo $domain; ?></td>
                            <td class="left" data-label="<?php e__('FILE_PATH'); ?>">
                                <?php foreach((array) $datas['file_paths'] as $path){
                                    if( empty($path) ) continue;
                                ?>
                                    <p><?php echo $path; ?></p>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>

                        </tbody>

                        </table>
                    </div>

                    <div class="debug_table_wrap">
                        <table class="debug_table">
                        <caption>
                        <?php e__('Language File'); ?>
                        </caption>
                        <thead>
                        <tr>
                            <th scope="col"><?php e__('Domain'); ?></th>
                            <th scope="col"><?php e__('Original String'); ?></th>
                            <th scope="col"><?php e__('Translation'); ?></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        foreach((array) $l10n as $domain=>$datas){

                        if( empty($datas) ) continue;

                            foreach((array) $datas['messages'] as $key=>$translates){
                        ?>
                        <tr>
                            <td scope="row" data-label="<?php e__('Domain'); ?>"><?php echo $domain; ?></td>
                            <td class="left" data-label="<?php e__('Original String'); ?>"><?php echo $key; ?></td>
                            <td class="left" data-label="<?php e__('Translation'); ?>">
                                <?php foreach((array) $translates as $num=>$translate){
                                    if( empty($translate) ) continue;
                                ?>
                                    <p><span class="num"><?php echo $num; ?></span><?php echo $translate; ?></p>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                            }   //end foreach
                        }   //end foreach
                        ?>

                        </tbody>

                        </table>
                    </div>
                </div>
            </div> <!-- end #debug_language_info -->

            <div id="debug_hook_info" class="inner_debug">
            <?php
            $event_totals = get_hook_datas('event');
            $event_callbacks = get_hook_datas('event', 1);
            $replace_totals = get_hook_datas('replace');
            $replace_callbacks = get_hook_datas('replace', 1);
            ?>
                <div class="hook_list">

                    <div class="debug_table_wrap">
                        <table class="debug_table hook_table">
                        <caption>
                        <?php e__('Hook Lists'); ?>
                        </caption>
                        <thead>
                        <tr>
                            <th scope="col" width="20%"><?php e__('event_tag (count)'); ?></th>
                            <th scope="col" width="60%"><?php e__('event_function'); ?></th>
                            <th scope="col" width="10%"><?php e__('argument_count'); ?></th>
                            <th scope="col" width="10%"><?php e__('priority'); ?></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        foreach((array) $event_totals as $tag=>$count){
                        
                        if( $tag === 'count' ) continue;
                        if( empty($count) ) continue;
                        
                        $datas = array();
                        if( isset($event_callbacks[$tag]) ){
                            
                            $event_callbacks_tag = $event_callbacks[$tag];
                            ksort($event_callbacks_tag);
                            
                            foreach((array) $event_callbacks_tag as $priority=>$event_args){
                                if( empty($event_args) ) continue;
                                    
                                    foreach($event_args as $index=>$funcs){
                                        $datas[] = array(
                                            'priority' => $priority,
                                            'function' => $funcs['function'],
                                            'arguments' => $funcs['arguments'],
                                            );
                                    }   //end foreach
                            }   //end foreach

                            $rowspan = $datas ? ' rowspan='.count($datas) : '';
                        
                            $is_print = $rowspan;
                            
                            foreach($datas as $data){
                        ?>
                        <tr>
                            <?php if ($is_print){ ?>
                            <td scope="row" data-label="<?php e__('event_tag'); ?>" <?php echo $rowspan; ?>><?php echo $tag.' <span class="hook_count">('.$count.')</span>'; ?></td>
                            <?php } ?>
                            <td data-label="<?php e__('event_function'); ?>">
                                <?php echo $data['function']; ?>
                            </td>
                            <td data-label="<?php e__('argument_count'); ?>"><?php echo $data['arguments']; ?></td>
                            <td data-label="<?php e__('priority'); ?>"><?php echo $data['priority']; ?></td>
                        </tr>
                        <?php
                                $is_print = '';
                                }   //end foreach
                            } else {    // else if
                        ?>
                        <tr>
                            <td scope="row" data-label="<?php e__('event_tag'); ?>"><?php echo $tag.' <span class="hook_count">('.$count.')</span>'; ?></td>
                            <td data-label="<?php e__('event_function'); ?>">&nbsp;</td>
                            <td data-label="<?php e__('argument_count'); ?>">&nbsp;</td>
                            <td data-label="<?php e__('priority'); ?>">&nbsp;</td>
                        </tr>
                        <?php
                            }//end if
                        }   //end foreach
                        ?>

                        </tbody>

                        </table>
                    </div>

                    <div class="debug_table_wrap">
                        <table class="debug_table hook_table">
                        <caption>
                        <?php e__('Hook Lists'); ?>
                        </caption>
                        <thead>
                        <tr>
                            <th scope="col" width="20%"><?php e__('replace_tag (count)'); ?></th>
                            <th scope="col" width="60%"><?php e__('replace_function'); ?></th>
                            <th scope="col" width="10%"><?php e__('argument_count'); ?></th>
                            <th scope="col" width="10%"><?php e__('priority'); ?></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        foreach((array) $replace_totals as $tag=>$count){
                        
                        if( $tag === 'count' ) continue;
                        if( empty($count) ) continue;

                        $datas = array();
                        if( isset($replace_callbacks[$tag]) ){
                            
                            $replace_callbacks_tag = $replace_callbacks[$tag];
                            ksort($replace_callbacks_tag);
                            
                            foreach((array) $replace_callbacks_tag as $priority=>$replace_args){
                                if( empty($replace_args) ) continue;
                                    
                                    foreach($replace_args as $index=>$funcs){
                                        $datas[] = array(
                                            'priority' => $priority,
                                            'function' => $funcs['function'],
                                            'arguments' => $funcs['arguments'],
                                            );
                                    }   //end foreach
                            }   //end foreach

                            $rowspan = $datas ? ' rowspan='.count($datas) : '';
                        
                            $is_print = $rowspan;
                            
                            foreach($datas as $data){
                        ?>
                        <tr>
                            <?php if ($is_print){ ?>
                            <td scope="row" data-label="<?php e__('replace_tag'); ?>" <?php echo $rowspan; ?>><?php echo $tag.' <span class="hook_count">('.$count.')</span>'; ?></td>
                            <?php } ?>
                            <td data-label="<?php e__('replace_function'); ?>">
                                <?php echo $data['function']; ?>
                            </td>
                            <td data-label="<?php e__('argument_count'); ?>"><?php echo $data['arguments']; ?></td>
                            <td data-label="<?php e__('priority'); ?>"><?php echo $data['priority']; ?></td>
                        </tr>
                        <?php
                                $is_print = '';
                                }   //end foreach
                            } else {    // else if
                        ?>
                        <tr>
                            <td scope="row" data-label="<?php e__('replace_tag'); ?>"><?php echo $tag.' <span class="hook_count">('.$count.')</span>'; ?></td>
                            <td data-label="<?php e__('replace_function'); ?>">&nbsp;</td>
                            <td data-label="<?php e__('argument_count'); ?>">&nbsp;</td>
                            <td data-label="<?php e__('priority'); ?>">&nbsp;</td>
                        </tr>
                        <?php
                            }//end if
                        }   //end foreach
                        ?>

                        </tbody>

                        </table>
                    </div>

                </div>  <!-- end .hook_list -->
            </div>

        </div>  <!-- end .content_inner -->
    </div>  <!-- end .debug_bar_content -->
</div>  <!-- end .debug_bar_wrap -->
<script>
jQuery(function($){
    $(".debug_tab").on("click", function() {
        $(".inner_debug").hide();
        $(this).addClass("active").siblings().removeClass("active");
        $("#debug_" + $(this).attr('data-tab')).show();
    });

    $(".debug_tab").on("click", "a", function(e) {
        e.preventDefault();
    });
    
    $(".debug_bar_wrap").on("click", ".debugbar_close_btn", function() {
        $(".view_debug_bar").trigger("click");
    })
    .on("click", ".view_debug_bar", function() {
        $(".debug_bar_content").toggle();
    });
});
</script>