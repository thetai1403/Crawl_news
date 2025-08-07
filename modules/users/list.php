<?php
if(!defined('_TAI')) {
    die('Truy cập không hợp lệ');
}

//?module=users&action=list&group=1&keyword=tai

$data = [
    'title' => 'Danh sách người dùng'
];
layout('header', $data);
layout('sidebar');

$filter = filterData();
$chuoiWhere = '';
$group = '0';
$keyword = '';

if(isGet()){
    if(isset($filter['keyword'])){
        $keyword = $filter['keyword'];
    }
    if(isset($filter['group'])){
        $group = $filter['group'];
    }

    if(!empty($keyword)){
        if(strpos($chuoiWhere, 'WHERE') == false){
            $chuoiWhere .= ' WHERE ';
        }else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= "fullname LIKE '%$keyword%' OR email LIKE '%$keyword%'";
}

    if(!empty($group)){
        if(strpos($chuoiWhere, 'WHERE') == false){
            $chuoiWhere .= ' WHERE ';
        }else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= " group_id = $group "; 
    }
}


//lấy dữ diệu từ bảng users
$getDetailUser = getAll("SELECT a.id, a.fullname, a.email, a.created_at, b.name


FROM users a INNER JOIN `groups` b 
ON a.group_id = b.id $chuoiWhere
ORDER BY a.created_at DESC");

$getGroup = getAll("SELECT * FROM `groups`")


?>
<div class="container-fluid grid-user">

    <a href="?module=users&action=add" class="btn btn-success mb-3"><i class="fa-solid fa-plus"></i>Thêm mới người
        dùng</a>

    <form class="mb-3" action="" method="get">
        <input type="hidden" name="module" value="users">
        <input type="hidden" name="action" value="list">
        <div class="row">
            <div class="col-3 ">
                <select class="form-select form-control" name="group" id="">
                    <option value="">Nhóm người dùng</option>
                    <?php
                    foreach ($getGroup as $item):
                    ?>

                    <option value="<?php echo $item['id'];?>"
                        <?php echo($group == $item['id']) ? 'selected' : false; ?>>
                        <?php echo $item['name'];?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-7 ">
                <input type="text" class="form-control" value="<?php echo(!empty($keyword)) ? $keyword : false;?>"
                    name="keyword" placeholder="Nhập thông tin tìm kiếm...">
            </div>
            <div class="col-2"><button class="btn btn-primary" type="submit">Tìm kiếm</button></div>
        </div>
    </form>
    <table class="table table-bordered text-center w-100">
        <thead>
            <tr>
                <th scope="col">STT</th>
                <th scope="col">Họ tên</th>
                <th scope="col">Email</th>
                <th scope="col">Ngày đăng ký</th>
                <th scope="col">Nhóm</th>
                <th scope="col">Phân quyền</th>
                <th scope="col">Sửa</th>
                <th scope="col">Xóa</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach ($getDetailUser as $key => $item):
            ?>
            <tr>
                <th scope="row"><?php echo $key + 1;?></th>
                <td><?php echo $item['fullname']; ?></td>
                <td><?php echo $item['email'];?></td>
                <td><?php echo $item['created_at'];?></td>
                <td><?php echo $item['name'];?></td>
                <td><a href="?module=users&action=permission&id=<?php echo $item['id']?>" class="btn btn-primary">Phân
                        quyền</a></td>
                <td><a href="?module=users&action=edit&id=<?php echo $item['id']?>" class="btn btn-warning"><i
                            class="fa-solid fa-pencil"></i></a></td>
                <td><a href="?module=users&action=delete&id=<?php echo $item['id']?>"
                        onclick="return confirm('Bạn có chắc chắn muốn xóa không ?')" class="btn btn-danger"><i
                            class="fa-solid fa-trash"></i></a></td>
            </tr>
            <?php
                endforeach;
            ?>
        </tbody>
    </table>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <li class="page-item"><a class="page-link" href="#">Previous</a></li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
        </ul>
    </nav>

</div>

<?php
layout('footer');

?>