<?php
if(!defined('_TAI')) {
    die('Truy cập không hợp lệ');
}

$data = [
    'title' => 'Chỉnh sửa người dùng'
];
layout('header', $data);
layout('sidebar');
$getData = filterData('get');
if(!empty($getData['id'])){
    
    $user_id = $getData['id'];
    $detailUser = getOne("SELECT * FROM users WHERE id = $user_id");
    if(empty($detailUser)){
        setSessionFlash('msg', 'Người dùng không tồn tại');
        setSessionFlash('msg_type', 'danger');
        redirect('?module=users&action=list');
    }

}else{
    setSessionFlash('msg', 'Đã có lỗi xảy ra, vui lòng thử lại sau');
        setSessionFlash('msg_type', 'danger');
        redirect('?module=users&action=list');
}

if(isPost()){
    $filter = filterData();
    $errors = [];

    // validate fullname
    if(empty(trim($filter['fullname']))){
        $errors['fullname']['required'] = 'Họ tên bắt buộc phải nhập';
    }else{
        if(strlen(trim(($filter['fullname'])))<5){
            $errors['fullname']['length'] = 'Họ tên phải hơn 5 kí tự';
        }
    }

    if($filter['email'] != $detailUser['email']){
         //Validate email 
        if(empty(trim($filter['email']))){
            $errors['email']['required'] = 'Email bắt buộc phải nhập';
        }else {
            //Đúng định dạng email, email tồn tại chưa
            if(!validateEmail(trim($filter['email']))){
            $errors['email']['isEmail'] = 'Email không đúng định dạng';
            }else{
                $email = $filter['email'];
    
                $checkEmail = getRows("SELECT * FROM users WHERE email = '$email'");
                if($checkEmail > 0){ 
                    $errors['email']['check'] = 'Email đã tồn tại';
                }
            }
        }
    }
   
    

    //Validate phone
    if(empty($filter['phone'])){
        $errors['phone']['required'] = 'Số điện thoại bắt buộc phải nhập';
    }else {
        if (!isPhone($filter['phone'])){
            $errors['phone']['isPhone'] = 'Số điện thoại không đúng định dạng';
        }
    }

    //Validate password
    if(empty(trim($filter['password']))){
        if (strlen(trim($filter['password']))<6){
            $errors['password']['length'] = 'Mật khẩu phải lớn hơn 6 kí tự';
        }
    }
    if(empty($errors)){

        



        $dataUpdate = [
            'fullname' => $filter['fullname'],
            'email' => $filter['email'],
            'phone' => $filter['phone'],
            'address' => (!empty($filter['address']) ? $filter['address'] : null),
            'group_id' => $filter['group_id'],
            'status' => $filter['status'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if(!empty($filter['password'])){
            $dataUpdate['password'] = password_hash($filter['password'], PASSWORD_DEFAULT);
        }
        $condition = " id=". $user_id;
       $updateStatus = update('users', $dataUpdate,$condition);

        if($updateStatus){
            setSessionFlash('msg', 'Chỉnh sửa người dùng thành công');
            setSessionFlash('msg_type', 'success');
            redirect('?module=users&action=list');
        }else {
            setSessionFlash('msg', 'Chỉnh sửa dùng thất bại');
        setSessionFlash('msg_type', 'danger');
        }

    }else {
        setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);

    }
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');

$oldData = getSessionFlash('oldData');
if(!empty($detailUser)){
    $oldData = $detailUser;
}
$errorsArr =  getSessionFlash('errors');
?>
<div class="container add-user">
    <h2>Chỉnh sửa người dùng</h2>
    <hr>
    <?php 
                if(!empty($msg) && !empty($msg_type)){
                    getMsg($msg,$msg_type);
                }
                
                ?>
    <form action="" method="post">
        <div class="row">
            <div class="col-6 pb-3">
                <label for="fullname">Họ và tên</label>
                <input id="fullname" name="fullname" type="text" class="form-control" value="<?php 
                        if(!empty($oldData)){
                            echo oldData($oldData, 'fullname');
                        }
                        ?>" placeholder="Nhập họ và tên">
                <?php 
                        if(!empty($errorsArr)){
                            echo formError($errorsArr, 'fullname');
                        }
                        ?>
            </div>
            <div class="col-6 pb-3">
                <label for="email">Email</label>
                <input id="email" name="email" type="text" class="form-control" value="<?php 
                        if(!empty($oldData)){
                            echo oldData($oldData, 'email');
                        }
                        ?>" placeholder="Nhập email">
                <?php 
                        if(!empty($errorsArr)){
                            echo formError($errorsArr, 'email');
                        }
                        ?>
            </div>
            <div class="col-6 pb-3">
                <label for="phone">Số điện thoại</label>
                <input id="phone" name="phone" type=" text" class="form-control" value="<?php 
                        if(!empty($oldData)){
                            echo oldData($oldData, 'phone');
                        }
                        ?>" placeholder="Nhập số điện thoại">
                <?php 
                        if(!empty($errorsArr)){
                            echo formError($errorsArr, 'phone');
                        }
                        ?>
            </div>
            <div class="col-6 pb-3">
                <label for="password">Mật khẩu</label>
                <input id="password" name="password" type="password" class="form-control" value="<?php 
                        if(!empty($oldData)){
                            echo oldData($oldData, 'password');
                        }
                        ?>" placeholder="Nhập mật khẩu">
                <?php 
                        if(!empty($errorsArr)){
                            echo formError($errorsArr, 'password');
                        }
                        ?>
            </div>
            <div class="col-6 pb-3">
                <label for="address">Địa chỉ</label>
                <input id="address" name="address" type="text" class="form-control" value="<?php 
                        if(!empty($oldData)){
                            echo oldData($oldData, 'address');
                        }
                        ?>" placeholder="Nhập địa chỉ">
            </div>
            <div class="col-3 pb-3">
                <label for="group">Phân cấp người dùng</label>
                <select name="group_id" id="group" class="form-select form-control">
                    <?php 
                $getGroups = getAll("SELECT * FROM `groups`");
                foreach($getGroups as $item):
                ?>
                    <option value="<?php echo $item['id']; ?>"
                        <?php echo ($oldData['group_id']==$item['id']) ? 'selected' : false; ?>>
                        <?php echo $item['name'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="col-3 pb-3">
                <label for="status">Trạng thái tài khoản</label>
                <select name="status" id="status" class="form-select form-control">
                    <option value="0" <?php echo ($oldData['status']==0) ? 'selected' : false; ?>>Chưa kích
                        hoạt</option>
                    <option value="1" <?php echo ($oldData['status']==1) ? 'selected' : false; ?>>Đã kích hoạt
                    </option>

                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-success px-4" style="min-width: 200px;">Xác nhận</button>
        <a type="button" href="?module=users&action=list" class="btn btn-primary">Quay lại</a>

    </form>
</div>

<?php
layout('footer');
?>