<?php

/*
	Question2Answer 1.4.2 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-plugin/example-page/qa-example-page.php
	Version: 1.4.2
	Date: 2011-09-12 10:46:08 GMT
	Description: Page module class for example page plugin


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/

	class qa_faq_page {
		
		var $directory;
		var $urltoroot;
		
		function load_module($directory, $urltoroot)
		{
			$this->directory=$directory;
			$this->urltoroot=$urltoroot;
		}
		
		function suggest_requests() // for display in admin interface
		{	
			return array(
				array(
					'title' => 'FAQ',
					'request' => 'faq',
					'nav' => 'M', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
				),
			);
		}
		
		function match_request($request)
		{
			$faq = qa_opt('faq_page_url');
			if ($request==$faq)
				return true;
			return false;
		}
		
		function process_request($request)
		{
			$qa_content=qa_content_prepare();

			$qa_content['head_lines'][]='<style>'.qa_opt('faq_css').'</style>';

			$qa_content['title']=qa_opt('faq_page_title');

			$qa_content['custom_0']=$this->filter_subs(qa_opt('faq_pre_html'));
			
			$idx = 0;
			while(qa_opt('faq_section_'.$idx)) {
				$title = $this->filter_subs(qa_opt('faq_section_'.$idx.'_title'));
				$text = $this->filter_subs(qa_opt('faq_section_'.$idx));

				$qa_content['custom_'.$idx.'_title']='<div id="custom_'.$idx.'_title" onclick="jQuery(\'#custom_'.$idx.'_text\').toggle(\'fast\')" class="qa-faq-section-title">'.$title.'</div>';
				$qa_content['custom_'.$idx.'_text']='<div id="custom_'.$idx.'_text" class="qa-faq-section-text">'.$text.'</div>';
				$idx++;
			}

			$qa_content['custom_'.$idx]=$this->filter_subs(qa_opt('faq_post_html'));

			return $qa_content;
		}
	
		function filter_subs($text) {
			
			// text subs
			
			$subs = array(
				'site_title' => qa_opt('site_title'),
				'site_url' => qa_opt('site_url'),
			);
			
			foreach($subs as $i => $v) {
				$text = str_replace('^'.$i,$v,$text);
			}

			// function subs

			preg_match_all('/\^qa_path\(([^)]+)\)/',$text,$qa_path,PREG_SET_ORDER);
			
			foreach($qa_path as $match) {
				$text = str_replace($match[0],qa_path($match[1]),$text);
			}

			preg_match_all('/\^qa_opt\(([^)]+)\)/',$text,$qa_opt,PREG_SET_ORDER);
			
			foreach($qa_opt as $match) {
				$text = str_replace($match[0],qa_opt($match[1]),$text);
			}
			
			// table subs
			
			if(strpos($text,'^pointstable') !== false) {
			
				require_once QA_INCLUDE_DIR.'qa-db-points.php';

				$optionnames=qa_db_points_option_names();
				$options=qa_get_options($optionnames);
				
				$table = '
<table class="qa-form-wide-table">
	<tbody>';
				foreach ($optionnames as $optionname) {
					
					switch ($optionname) {
						case 'points_multiple':
							$prefix='&#215;';
							break;
							
						case 'points_per_q_voted':
						case 'points_per_a_voted':
							$prefix='&#177;';
							break;
							
						case 'points_q_voted_max_gain':
						case 'points_a_voted_max_gain':
							$prefix='+';
							break;
						
						case 'points_q_voted_max_loss':
						case 'points_a_voted_max_loss':
							$prefix='&ndash;';
							break;
							
						case 'points_base':
							$prefix='+';
							break;
							
						default:
							$prefix='<SPAN STYLE="visibility:hidden;">+</SPAN>'; // for even alignment
							break;
					}
					
					$table .= '
		<tr>
			<td class="qa-form-wide-label">
				'.qa_lang_html('options/'.$optionname).'
			</td>
			<td class="qa-form-wide-data" style="text-align:right">
				<span class="qa-form-wide-prefix"><span style="width: 1em; display: -moz-inline-stack;">'.$prefix.'</span></span>
				'.qa_html($options[$optionname]).($optionname=='points_multiple'?'':'
				<span class="qa-form-wide-note">'.qa_lang_html('admin/points').'</span>').'
			</td>
		</tr>';
				}
				
				$table .= '
	</tbody>
</table>';
			
				
				$text = str_replace('^pointstable',$table,$text);
			
			}

			if(strpos($text,'^privilegestable') !== false) {


				$options = qa_get_permit_options();
				
				foreach ($options as $option) {
					if(qa_opt($option) == QA_PERMIT_POINTS) {
						$popts[$option] = (int)qa_opt($option.'_points');
					}
				}
				
				if(isset($popts)) {
				
					asort($popts);

					$table = '
	<table class="qa-form-wide-table">
		<tbody>';
					foreach ($popts as $key => $val) {
						
						$table .= '
			<tr>
				<td class="qa-form-wide-label">
					'.qa_lang_html('profile/'.$key).'
				</td>
				<td class="qa-form-wide-data" style="text-align:right">
					'.qa_html($val).'
					<span class="qa-form-wide-note">'.qa_lang_html('admin/points').'</span>'.'
				</td>
			</tr>';
					}
					
					$table .= '
		</tbody>
	</table>';
					$text = str_replace('^privilegestable',$table,$text);
				}
				else $text = str_replace('^privilegestable','',$text);
			}
				
			return $text;
		}
	
	};
	

/*
	Omit PHP closing tag to help avoid accidental output
*/