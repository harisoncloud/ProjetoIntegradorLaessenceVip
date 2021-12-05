<?php

class ControllerCatalogMagic360Gallery extends Controller {

    private $error = array();
    
    public function index(){
    
        //set rights for that controller
        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'catalog/magic360_gallery');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'catalog/magic360_gallery');
        
        $data = array();
        
        if (version_compare(VERSION, '2', '<')){
                        $data['ocVersion'] = '1.5';
        } else if (version_compare(VERSION, '3', '<')){
                        $data['ocVersion'] = '2';
        } else {
                        $data['ocVersion'] = '3';
        }
        
        if($data['ocVersion'] == '1.5'){
            $this->language->load('catalog/magic360_gallery');
        
            $this->data['text_upload']       = $this->language->get('text_upload');
            $this->data['text_images_block'] = $this->language->get('text_images_block');
            $this->data['text_number']       = $this->language->get('text_number');
            $this->data['text_image']        = $this->language->get('text_image');
            $this->data['text_delete']       = $this->language->get('text_delete');
            $this->data['text_delete_all']   = $this->language->get('text_delete_all');
            $this->data['text_save']         = $this->language->get('text_save');
            
            $this->data['token'] = $this->session->data['token'];
            $this->template = 'catalog/magic360_gallery.tpl';
            $this->render();
        }else{
        
            

            $this->load->language('catalog/magic360_gallery');
            
            $data['text_upload']       = $this->language->get('text_upload');
            $data['text_images_block'] = $this->language->get('text_images_block');
            $data['text_number']       = $this->language->get('text_number');
            $data['text_image']        = $this->language->get('text_image');
            $data['text_delete']       = $this->language->get('text_delete');
            $data['text_delete_all']   = $this->language->get('text_delete_all');
            $data['text_save']         = $this->language->get('text_save');

            
            //$data['action'] = $this->url->link('catalog/magic360_gallery/getPostRequest', 'token=' . $this->session->data['token'], 'SSL');
            if ($data['ocVersion'] == '2') {
                $data['token'] = $this->session->data['token'];
                return $this->load->view('catalog/magic360_gallery.tpl', $data);       
            } else if ($data['ocVersion'] == '3') {
                $data['token'] = $this->session->data['user_token'];
                return $this->load->view('catalog/magic360_gallery', $data); //twig
            }
        }
        //$this->load->model('catalog/magic360_gallery');
    }
    

    public function uploadImages(){

        $this->load->model('catalog/magic360_gallery');

        $magic360Images = array();
        $magic360Columns = 0;
        $multiRows = false;

        $imageBasePath = DIR_IMAGE . 'magic360/';
        $imageBaseUrl = HTTP_CATALOG . 'image/magic360/';

        $product_id = $this->request->post['product_id'];
        $multiRows = $this->request->post['multiRows'] == 'true'? true : false;

        if(!empty($this->request->files) && (is_dir($imageBasePath.$product_id."/") || mkdir($imageBasePath . $product_id ."/", 0777, true))){

            foreach ($this->request->files as $file) {
                if(move_uploaded_file($file['tmp_name'], $imageBasePath . $product_id ."/". $file['name'])) {
                    chmod($imageBasePath.$product_id."/".$file['name'], 0777);
                    $magic360Images[] = $file['name'];
                            }
            }

            $data['images'] = implode(';', $magic360Images);
            if($multiRows){
                $data['columns'] = $this->request->post['columns'];
            }else{
                $data['columns'] = count($magic360Images);    
            }
            $data['multiRows'] = $multiRows;
            
            $this->updateDB($product_id, $data);

            $data = $this->model_catalog_magic360_gallery->getRow($product_id);
            $data['imageBaseUrl'] = $imageBaseUrl;
            $data['multiRows'] = $this->checkMultiRows($data);

            $this->response->setOutput(json_encode($data));

        }

         
    }

    public function getImageContent(){
        $this->load->model('catalog/magic360_gallery');

        $imageBaseUrl = HTTP_CATALOG . 'image/magic360/';

        if(!empty($this->request->post['product_id'])){
            $product_id = $this->request->post['product_id'];
            $data = $this->model_catalog_magic360_gallery->getRow($product_id);
            if(!empty($data)){
                $data['imageBaseUrl'] = $imageBaseUrl;
                $data['multiRows'] = $this->checkMultiRows($data);
            }else{
                $data['images'] = '';
                $data['columns'] = '';
            }

            $this->response->setOutput(json_encode($data));
        }

    }


    public function deleteImages(){

        $imageBasePath = DIR_IMAGE . 'magic360/';

        $this->load->model('catalog/magic360_gallery');

        if(!empty($this->request->post['product_id'])){
            $product_id = $this->request->post['product_id'];
            $images = $this->request->post['images'];

            if($this->request->post['delete_all_images_flag'] == 'true'){
                $result = $this->model_catalog_magic360_gallery->deleteRow($product_id);  

                $images = explode(';', $images);
                foreach ($images as $image) {
                    unlink($imageBasePath . $product_id ."/". $image);
                }
                rmdir($imageBasePath . $product_id ."/");

                $this->response->setOutput($result);
            }else{
                $columns = $this->request->post['columns'];
                $deletedImage = $this->request->post['deleted_image'];
                if($images == ""){
                    $result = $this->model_catalog_magic360_gallery->deleteRow($product_id); 
                    unlink($imageBasePath . $product_id ."/". $deletedImage);
                    rmdir($imageBasePath . $product_id ."/");  
                    $this->response->setOutput($result);
                }else{
                    $result = $this->model_catalog_magic360_gallery->updateRow($product_id, $images, $columns);
                    unlink($imageBasePath . $product_id ."/". $deletedImage);
                    $this->response->setOutput($result);
                }
            }
            
        }
    }


    public function updateColumns(){

        $this->load->model('catalog/magic360_gallery');

        if(!empty($this->request->post['product_id'])){
            $product_id = $this->request->post['product_id'];
            $images = $this->request->post['images'];
            $columns = $this->request->post['columns'];

            $this->model_catalog_magic360_gallery->updateRow($product_id, $images, $columns);

            $this->response->setOutput("Columns updated");
        }

    }


    protected function updateDB($product_id, $data){
        $this->load->model('catalog/magic360_gallery');

        if(!$this->model_catalog_magic360_gallery->checkRow($product_id)){
            $this->model_catalog_magic360_gallery->addRow($product_id, $data['images'], $data['columns']);
        }else{
            $oldData = $this->model_catalog_magic360_gallery->getRow($product_id);
            if($data['multiRows']){
                $this->model_catalog_magic360_gallery->updateRow($product_id, $oldData['images'] .';'. $data['images'], (int)$data['columns']);
            }else{
                $this->model_catalog_magic360_gallery->updateRow($product_id, $oldData['images'] .';'. $data['images'], (int)$oldData['columns'] + (int)$data['columns']);
            }
            
        }


    }


    protected function checkMultiRows($data){
        $images = $data['images'];
        $columns = $data['columns'];
        
        return count(explode(';', $images)) != (int)$columns ? true : false;
    }
}