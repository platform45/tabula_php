<?php

/* * ****************** PAGE DETAILS ******************* */
/* @Programmer  : TUSHAR KOCHAR
 * @Maintainer  : TUSHAR KOCHAR
 * @Created     : 5 Aug 2016
 * @Modified    : 
 * @Description : This is channel controller which is used
 * to show top 10 channels as well as all channels.
 * ****************************************************** */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Video_search extends REST_Controller {

   
    function __construct() {
        parent::__construct();
        $api_key = $this->input->post('api_key');
        if ($api_key) {
            $apiStatus = validateApiKey(md5($api_key));
            //echo $apiStatus;die;
            $apiStatus = json_decode($apiStatus);
            if ($apiStatus->status != SUCCESS) {
                echo json_encode($apiStatus);
                die;
            }
        } else {
            echo json_encode(array("status" => FAIL, "message" => NO_TOKEN_MESSAGE));
            die;
        }

        $this->load->model('webservices/videomodel', 'videomodel', TRUE);
    }

    /*     * ***********************
     * Method Name: getChnnels
     * Purpose: To show all  Channels.         
     * ** */

    public function getChannels_POST() {


        $channelDetails = $this->videomodel->getChannels();
        if ($channelDetails) {
            $channelArr['status'] = SUCCESS;
            $channelArr['message'] = SUCCESS_CHANNELS;

            foreach ($channelDetails as $aVal) {
                $aResult['channel_id'] = $aVal['channel_id'];
                $aResult['channel_title'] = stripslashes($aVal['channel_title']);
                $aResult['channel_oneliner'] = stripslashes($aVal['channel_oneliner']);
                if ($aVal['channel_image'] != '')
                    $aResult['channel_image'] = base_url() . BRAND_IMAGE_PATH . $aVal['channel_image'];
                else
                    $aResult['channel_image'] = "";

                $aPopularChannels[] = $aResult;
            }
            $channelArr['channels'] = $aPopularChannels;
        }
        else {
            $channelArr['status'] = FAIL;
            $channelArr['message'] = NO_CHANNELS;
        }

        $this->response($channelArr); // 404 being the HTTP response code
    }

    /*     * ***********************
     * Method Name: getCategories
     * Purpose: To get categories according to channel .         
     * ** */

    public function getCategories_POST() {
        $channel_id = $this->post('channel_id') ? $this->post('channel_id') : "";
        if (!$channel_id)
            $channel_id = 0;
        $categoriesDetails = $this->videomodel->getCategories($channel_id);
        if ($categoriesDetails) {
            $catArr['status'] = SUCCESS;
            $catArr['message'] = SUCCESS_CATEGORIES;
            $catArr['categories'] = $categoriesDetails;
        } else {
            $catArr['status'] = FAIL;
            $catArr['message'] = NO_CATEGORIES;
        }

        $this->response($catArr); // 404 being the HTTP response code
    }

    // Video search according to channel, category and search Keyword
    public function getVideoSearch_POST() {
        $channel_id = $this->post('channel_id') ? $this->post('channel_id') : "";
        $category_id = $this->post('category_id') ? $this->post('category_id') : "";
        $search_keyword = $this->post('search_keyword') ? $this->post('search_keyword') : "";
        $page_index = $this->post('page_index') ? $this->post('page_index') : "";


        //echo $search_keyword;die;
        $videoDetails = $this->videomodel->getVideo($channel_id, $category_id, $search_keyword,$page_index);
        if ($videoDetails) {
          
            foreach ($videoDetails as $aVal) {

                $aResultChannel['channel_id'] = stripslashes($channel_id);
                $aResultChannel['channel_title'] = stripslashes($aVal['channel_title']);
                $aResultChannel['channel_description'] = stripslashes($aVal['channel_oneliner']);
                $aResultChannel['channel_share_count'] = stripslashes($aVal['channel_share_count']);
                $aResultChannel['channel_created_date'] = findTime(stripslashes($aVal['channel_date']));
                $aResultChannel['channel_is_purchased'] = 0;
              
                if ($aVal['channel_image'])
                    $aResultChannel['channel_thumb'] = base_url() . "assets/upload/brand/" . $aVal['channel_image'];

                if ($aVal['channel_icon'])
                    $aResultChannel['channel_icon'] = base_url() . "assets/upload/icon/" . $aVal['channel_icon'];

                $aResult['category_id'] = stripslashes($aVal['category_id']);
                $aResult['category_title'] = stripslashes($aVal['category_name']);

                if ($aVal['category_icon'])
                $aResult['category_icon'] = base_url() . "assets/upload/icon/" . $aVal['category_icon'];
                $aResult['video_description'] = stripslashes($aVal['video_desc']);
                $aResult['vid_id']            = $aVal['vid_id'];
                $aResult['video_title'] = stripslashes($aVal['video_title']);
                $aResult['video_share_count'] = stripslashes($aVal['video_share_count']);
                $aResult['video_date'] = findTime(stripslashes($aVal['created_on']));
                $aResult['video_ad'] = $this->videomodel->getAds($channel_id);
                $aResult['isPaid'] = stripslashes($aVal['video_type']);
                if ($aVal['video'] != '')
                    $aResult['video'] = base_url() . "assets/video/" . $aVal['video'];
                else
                    $aResult['video'] = "";

                $channel[] = $aResultChannel;
               
                $channels = array_splice($channel, 0, 1);
                $aVideos[] = $aResult;
            }
            $freesequential = $this->videomodel->getFreeVideos($aVal['channel_id']);
            if($freesequential)
            {
                foreach ($freesequential as $aVal) {

                    $aResultVideo['vid_id'] = stripslashes($aVal['vid_id']);
                    $aResultVideo['video_title'] = stripslashes($aVal['video_title']);
                    $aResultVideo['video_desc'] = stripslashes($aVal['video_desc']);
                    $aResultVideo['video_share_count'] = stripslashes($aVal['video_share_count']);
                    $aResultVideo['video_date'] = findTime(stripslashes($aVal['created_on']));
                    if ($aVal['video']) {
                        $aResultVideo['video'] = base_url() . "assets/video/" . $aVal['video'];
                    } else {
                        $aResultVideo['video'] = "";
                    }
                    $afreeVideos[] = $aResultVideo;
                }
            }
            else
            {
                $afreeVideos =array();
            }
            $videoArr['status'] = SUCCESS;
            $videoArr['message'] = SUCCESS_VIDEOS;
            $videoArr['channelList'] = $channels;
            $videoArr['sequential_videos'] = $afreeVideos;
            $videoArr['videoList'] = $aVideos;
        } else {
            $videoArr['status'] = FAIL;
            $videoArr['message'] = NO_CATEGORIES;
        }

        $this->response($videoArr); // 404 being the HTTP response code
    }
    
    // Get video by categories
       function getvideobyCategory_POST()
      {
        $channel_id = $this->post('channel_id') ? $this->post('channel_id') : "";
        $category_id = $this->post('category_id') ? $this->post('category_id') : "";
        $page_index = $this->post('page_index') ? $this->post('page_index') : "";
           
        $videoDetails = $this->videomodel->getVideobyCategory($channel_id, $category_id, $page_index);
        if ($videoDetails) {
          
            foreach ($videoDetails as $aVal) {

                $aResult['category_id'] = stripslashes($aVal['category_id']);
                $aResult['category_title'] = stripslashes($aVal['category_name']);

                if ($aVal['category_icon'])
                $aResult['category_icon'] = base_url() . "assets/upload/icon/" . $aVal['category_icon'];
                $aResult['video_description'] = stripslashes($aVal['video_desc']);
                $aResult['vid_id']            = $aVal['vid_id'];
                $aResult['video_title'] = stripslashes($aVal['video_title']);
                $aResult['video_date'] = findTime(stripslashes($aVal['created_on']));
                $aResult['video_share_count'] = stripslashes($aVal['video_share_count']);

                $aResult['video_ad'] = $this->videomodel->getAds($channel_id);
                $aResult['isPaid'] = stripslashes($aVal['video_type']);
                if ($aVal['video'] != '')
                    $aResult['video'] = base_url() . "assets/video/" . $aVal['video'];
                else
                    $aResult['video'] = "";

                $aVideos[] = $aResult;
            }
            
            $videoArr['status'] = SUCCESS;
            $videoArr['message'] = SUCCESS_VIDEOS;           
            $videoArr['videoList'] = $aVideos;
        } else {
            $videoArr['status'] = FAIL;
            $videoArr['message'] = NO_CATEGORIES;
        }

        $this->response($videoArr); // 404 being the HTTP response code
      }
      
      // to increase video share count
      function videoShareCount_POST()
      {
        $video_id = $this->post('video_id') ? $this->post('video_id') : "";
        $video_id = $this->videomodel->videoShareCount($video_id);
        if($video_id)
        {
            $videoArr['status'] = SUCCESS;
            $videoArr['message'] = SUCCESS_VIDEOS;
        }
        $this->response($videoArr);
        
      }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */