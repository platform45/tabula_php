<?php
/*
* Programmer Name:AD
* Purpose: Config file for news form validation control.
* Date:11 Oct 2014
* Dependency: None
*/

$config = array(
                        
                        'url' => array(
                                array(
                                        'field' => 'txtwebpageurl',
                                        'label' => 'Web Page URL',
                                        'rules' => 'required'
                                )
                            ),
			'content' => array(
                                array(
                                        'field' => 'en_txtbrowsertitle',
                                        'label' => 'Browser Title',
                                        'rules' => 'required'
                                ),
                                array(
                                        'field' => 'en_txtpagetitle',
                                        'label' => 'Page Title',
                                        'rules' => 'required'
                                ),
                                array(
                                        'field' => 'en_txtpageurl',
                                        'label' => 'Page URL',
                                        'rules' => 'required'
                                ),
                                array(
                                        'field' => 'en_txtContent',
                                        'label' => 'Content',
                                        'rules' => 'required'
                                ),
			),			
			'slider_en' => array(
                                array(
                                        'field' => 'sli_sequence',
                                        'label' => 'Sequence Required',
                                        'rules' => 'required'
                                )
                        ),
                        'frmOffMenu' => array(
                            array(  
                                    'field' => 'txtmaincontent',
                                    'label' => 'Content',
                                    'rules' => 'required'
                                ),
                            array(  
                                    'field' => 'txtheadertitle',
                                    'label' => 'Header title',
                                    'rules' => 'required'
                                ),
                            array(  
                                    'field' => 'txtsubtitle',
                                    'label' => 'Sub title',
                                    'rules' => 'required'
                                ),
                            array(  
                                    'field' => 'txtbrowsertitle',
                                    'label' => 'Browser title',
                                    'rules' => 'required'
                                ),
                            array(  
                                    'field' => 'txturlname',
                                    'label' => 'Urlname',
                                    'rules' => 'required'
                                ),
                            array(  
                                    'field' => 'txtmetadescription',
                                    'label' => 'Meta description',
                                    'rules' => 'required'
                                ),
                            array(  
                                    'field' => 'txtkeywords',
                                    'label' => 'Keywords',
                                    'rules' => 'required'
                                )
                        ),
                    'frmFaq' => array(
                            array(  
                                    'field' => 'txtquestion_en',
                                    'label' => 'Question',
                                    'rules' => 'required'
                                ),
                            array(  
                                    'field' => 'txtanswer_en',
                                    'label' => 'Answer',
                                    'rules' => 'required'
                                )
                        )
		);
?>