<?php
if(!defined('_TAI')) {
    die('Truy cập không hợp lệ');
}

$data = [
    'title' => 'Thêm mới khóa học'
];
layout('header', $data);
layout('sidebar');


if(isPost()){
    $filter = filterData();
    $errors = [];

    // validate name
    if(empty(trim($filter['name']))){
        $errors['name']['required'] = 'Tên khóa học bắt buộc phải nhập';
    }else{
        if(strlen(trim(($filter['name'])))<5){
            $errors['name']['length'] = 'Tên khóa học phải hơn 5 kí tự';
        }
    }

    // validate slug
    if(empty(trim($filter['slug']))){
        $errors['slug']['required'] = 'Slug bắt buộc phải nhập';
    }

    //Validate price
    if(empty($filter['price'])){
        $errors['price']['required'] = 'Giá bắt buộc phải nhập';
    }

    //Validate description
    if(empty($filter['description'])){
        $errors['description']['required'] = 'Mô tả bắt buộc phải nhập';
    }

    
    if(empty($errors)){

        //Xử lý thumbnail upload ảnh
        $uploadDir = 'templates/uploads/';
        if(!file_exists($uploadDir)){
            mkdir($uploadDir, 0777, true); //tạo thư mục nếu chưa tồn tại
        }
        
        $fileName = basename($_FILES['thumbnail']['name']);
        $targetFile = $uploadDir .time() . '-' . $fileName;

        $thumb = '';
        $checkMove = move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFile);
        if($checkMove){
            $thumb = $targetFile;
        }
        
        $dataInsert = [
            'name' => $filter['name'],
            'slug' => $filter['slug'],
            'price' => $filter['price'],
            'description' => $filter['description'],
            'thumbnail' => $thumb,
            'category_id' => $filter['category_id'],
            'created_at' => date('Y-m-d H:i:s')
        ];
       $insertStatus = insert('course', $dataInsert);

        if($insertStatus){
            setSessionFlash('msg', 'Thêm khóa học thành công');
            setSessionFlash('msg_type', 'success');
            redirect('?module=course&action=list');
        }else {
            setSessionFlash('msg', 'Thêm khóa học thất bại');
        setSessionFlash('msg_type', 'danger');
        }

    }else {
        setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);

    }

    $msg = getSessionFlash('msg');
    $msg_type = getSessionFlash('msg_type');
    $oldData = getSessionFlash('oldData');
    $errorsArr =  getSessionFlash('errors');
}
?>
<div class="container add-user">
    <h2>Thêm mới khóa học</h2>
    <hr>
    <?php 
                if(!empty($msg) && !empty($msg_type)){
                    getMsg($msg,$msg_type);
                }
                
                ?>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-6 pb-3">
                <label for="name">Tên khóa học</label>
                <input id="name" name="name" type="text" class="form-control" value="<?php 
                        if(!empty($oldData)){
                            echo oldData($oldData, 'name');
                        }
                        ?>" placeholder="Nhập khóa học">
                <?php 
                        if(!empty($errorsArr)){
                            echo formError($errorsArr, 'name');
                        }
                        ?>
            </div>
            <div class="col-6 pb-3">
                <label for="slug">Đường dẫn</label>
                <input id="slug" name="slug" type="text" class="form-control" value="<?php 
                        if(!empty($oldData)){
                            echo oldData($oldData, 'slug');
                        }
                        ?>" placeholder="Nhập slug">
                <?php 
                        if(!empty($errorsArr)){
                            echo formError($errorsArr, 'slug');
                        }
                        ?>
            </div>
            <div class="col-6 pb-3">
                <label for="description">Mô tả</label>
                <input id="description" name="description" type=" text" class="form-control" value="<?php 
                        if(!empty($oldData)){
                            echo oldData($oldData, 'description');
                        }
                        ?>" placeholder="Mô tả">
                <?php 
                        if(!empty($errorsArr)){
                            echo formError($errorsArr, 'description');
                        }
                        ?>
            </div>
            <div class="col-6 pb-3">
                <label for="price">Giá</label>
                <input id="price" name="price" type="text" class="form-control" value="<?php 
                        if(!empty($oldData)){
                            echo oldData($oldData, 'price');
                        }
                        ?>" placeholder="Nhập giá">
                <?php 
                        if(!empty($errorsArr)){
                            echo formError($errorsArr, 'price');
                        }
                        ?>
            </div>
            <div class="col-6 pb-3">
                <label for="thumbnail">Thumbnail</label>
                <input id="thumbnail" name="thumbnail" type="file" class="form-control">
                <img id="preview" src="" alt="">
            </div>
            <div class="col-3 pb-3">
                <label for="group">Lĩnh vực</label>
                <select name="category_id" id="group" class="form-select form-control">
                    <?php 
                $getGroups = getAll("SELECT * FROM `course_category`");
                foreach($getGroups as $item):
                ?>
                    <option value="<?php echo $item['id']; ?>"><?php echo $item['name'];?></option>
                    <?php endforeach;?>
                </select>
            </div>

        </div>
        <button type="submit" class="btn btn-success px-4" style="min-width: 200px;">Xác nhận</button>

    </form>
</div>

<?php
layout('footer');
?>