<?php
if(!defined('_TAI')) {
    die('Truy cập không hợp lệ');
}


$data = [
    'title' => 'Danh sách khóa học'
];
layout('header', $data);
layout('sidebar');

$filter = filterData();
$chuoiWhere = '';
$cate = '0';
$keyword = '';

if(isGet()){
    if(isset($filter['keyword'])){
        $keyword = $filter['keyword'];
    }
    if(isset($filter['cate'])){
        $cate = $filter['cate'];
    }

    if(!empty($keyword)){
        if(strpos($chuoiWhere, 'WHERE') == false){
            $chuoiWhere .= ' WHERE ';
        }else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= "a.name LIKE '%$keyword%' OR a.description LIKE '%$keyword%'";
}

    if(!empty($cate)){
        if(strpos($chuoiWhere, 'WHERE') == false){
            $chuoiWhere .= ' WHERE ';
        }else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= " a.category_id = $cate "; 
    }
}

//Xử lý phân trang
$maxData = getRows("SELECT id FROM course"); //Tổng dữ liệu
$perPage = 3 ; //Số dòng dữ liệu một trang
$maxPage=ceil($maxData/$perPage);
$offset = 0;
$page = 1;
//get page 
if(isset($filter['page'])){
    $page = $filter['page'];
}

if($page > $maxPage || $page < 1){
    $page = 1;
}

    $offset = ($page -1) * $perPage;

//echo $maxPage;


//lấy dữ diệu từ bảng users
$getDetailUser = getAll("SELECT a.id, a.name, a.price, a.created_at, a.thumbnail, b.name as cate_name
FROM course a INNER JOIN course_category b 
ON a.category_id = b.id $chuoiWhere
ORDER BY a.created_at DESC
LIMIT $offset, $perPage
");


//Xử lý Query
if(!empty($_SERVER)){
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('&page='.$page, '', $queryString);
}


$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
?>
<div class="container-fluid grid-user">

    <a href="?module=course&action=add" class="btn btn-success mb-3"><i class="fa-solid fa-plus"></i>Thêm mới khóa
        học</a>
    <?php 
                if(!empty($msg) && !empty($msg_type)){
                    getMsg($msg,$msg_type);
                }
                
                ?>
    <form class="mb-3" action="" method="get">
        <input type="hidden" name="module" value="course">
        <input type="hidden" name="action" value="list">
        <div class="row">
            <div class="col-3 ">
                <select class="form-select form-control" name="cate" id="">
                    <option value="">Lĩnh vực</option>
                    <?php
                    $getCate = getAll("SELECT * FROM course_category");
                    foreach ($getCate as $item):
                    ?>

                    <option value="<?php echo $item['id'];?>" <?php echo($cate == $item['id']) ? 'selected' : false; ?>>
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
                <th scope="col">Tên khóa học</th>
                <th scope="col">Thumbnail</th>
                <th scope="col">Giá</th>
                <th scope="col">Lĩnh vực</th>
                <th scope="col">Sửa</th>
                <th scope="col">Xóa</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach ($getDetailUser as $key => $item):
            ?>
            <tr>
                <th scope="row"><?php echo $key + 1;?></th>
                <td><?php echo $item['name']; ?></td>
                <td><img width="180px" src="<?php echo $item['thumbnail'];?>" alt=""></td>
                <td><?php echo $item['price'];?></td>
                <td><?php echo $item['cate_name'];?></td>
                <td><a href="?module=course&action=edit&id=<?php echo $item['id']?>" class="btn btn-warning"><i
                            class="fa-solid fa-pencil"></i></a></td>
                <td><a href="?module=course&action=delete&id=<?php echo $item['id']?>"
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
            <!-- Xử lý nút 'Trước' -->

            <?php 
            if($page>1):
            ?>
            <li class="page-item"><a class="page-link"
                    href="?<?php echo $queryString?>&page=<?php echo $page-1;?>">Trước</a></li>
            <?php 
            endif;
            ?>
            <!-- Tính vị trí bắt đầu -->

            <?php
                $start = $page - 1;
                if($start<1){
                    $start = 1;
                }
            ?>

            <?php 
            if($start>1):
            ?>
            <li class="page-item"><a class="page-link"
                    href="?<?php echo $queryString?>&page=<?php echo $page-1;?>">...</a></li>
            <?php 
            endif;

            $end = $page + 1;
            if($end>$maxPage){
                $end = $maxPage;
            }
            
            ?>


            <?php for($i = $start; $i<=$end; $i++):?>

            <li class="page-item <?php echo($page == $i) ? 'active': false?>"><a class="page-link"
                    href="?<?php echo $queryString?>&page=<?php echo $i; ?>"><?php echo $i;?></a>
            </li>

            <?php 
            endfor;
                if($end < $maxPage):
            ?>
            <li class="page-item"><a class="page-link"
                    href="?<?php echo $queryString?>&page=<?php echo $page+1;?>">...</a></li>

            <?php endif; ?>

            <!-- Xử lý nút 'Sau' -->


            <?php 
            if($page<$maxPage):
            ?>
            <li class="page-item"><a class="page-link"
                    href="?<?php echo $queryString?>&page=<?php echo $page+1;?>">Sau</a></li>
            <?php 
            endif;
            ?>
        </ul>
    </nav>

</div>

<?php
layout('footer');

?>