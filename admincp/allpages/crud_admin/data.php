<?php
// Database details
include ('../../../config/db_config.php');

// Get job (and id)
$job = '';
$id  = '';
if (isset($_REQUEST['job'])){
  $job = $_REQUEST['job'];
  if ($job == 'get_companies' ||
      $job == 'get_company'   ||
      $job == 'add_company'   ||
      $job == 'edit_company'  ||
      $job == 'delete_company'){
    if (isset($_REQUEST['id'])){
      $id = $_REQUEST['id'];
      if (!is_numeric($id)){
        $id = '';
      }
    }
  } else {
    $job = '';
  }
}

// Prepare array
$mysql_data = array();

// Valid job found
if ($job != ''){
   
  if (mysqli_connect_errno()){
    $result  = 'error';
    $message = 'Failed to connect to database: ' . mysqli_connect_error();
    $job     = '';
  }
  
  // Execute job
  if ($job == 'get_companies'){
    
    // Get companies
    $query = "SELECT * FROM `table_administrator` ";

    $query = mysqli_query($conn, $query);
    if (!$query){
      $result  = 'error';
      $message = 'query error';
    } else {
      $result  = 'success';
      $message = 'query success';
      while ($company = mysqli_fetch_array($query)){
        $functions  = '<div class="function_buttons"><ul>';
        $functions .= '<li class="function_edit"><a data-id="'   . $company['id_administrator'] . '" data-name="' . $company['administrator_name'] . '"><span>Edit</span></a></li>';
        $functions .= '<li class="function_delete"><a data-id="' . $company['id_administrator'] . '" data-name="' . $company['administrator_name'] . '"><span>Delete</span></a></li>';
        $functions .= '</ul></div>';

        $mysql_data[] = array(
          "id_administrator"  => $company['id_administrator'],          
          "administrator_name" => $company['administrator_name'],
          "administrator_phone" => $company['administrator_phone'],
          "administrator_email" => $company['administrator_email'],
          "administrator_avatar"   => $company['administrator_avatar'],
          "img_4display"  => '<img src="../'.$company['administrator_avatar'].'" class="img-thumbnail" width="80" height="80" />',
          "status" => $company['status'],
          "functions"     => $functions
        );
      }
    }
    
  } elseif ($job == 'get_company'){
    
    // Get company
    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
    } else {

      $query = "SELECT * FROM table_administrator WHERE id_administrator= '".mysqli_real_escape_string($conn, $id)."' ";
      $query = mysqli_query($conn, $query);
      
      if (!$query){
        $result  = 'error';
        $message = 'query error';
      } else {
        $result  = 'success';
        $message = 'query success';
        while ($company = mysqli_fetch_array($query)){
         
          if($company['status']=='active') $status_txt='Đã kích hoạt'; else $status_txt='Chưa kích hoạt';
          
          $mysql_data[] = array(
          "id_administrator"  => $company['id_administrator'],          
          "administrator_name" => $company['administrator_name'],
          "administrator_phone" => $company['administrator_phone'],
          "administrator_email" => $company['administrator_email'],
          "administrator_avatar" => $company['administrator_avatar'],
          "administrator_avatar"   => $company['administrator_avatar'],
          "img_4display"  => '<img src="../'.$company['administrator_avatar'].'" class="img-thumbnail" width="80" height="80" />',
          "status" => $company['status']
          );
        }
      }

    }
  
  } elseif ($job == 'add_company'){
      
// Add company
  $query = "INSERT INTO table_administrator SET ";
  if (isset($_REQUEST['administrator_name'])) 
  {
    $query .= "administrator_name  = '" . mysqli_real_escape_string($conn, $_REQUEST['administrator_name'])         . "', ";
  }

// xu ly file hinh dai dien
if(is_uploaded_file($_FILES['user_image']['tmp_name']))
{ 
    // do this, upload file
    $target_dir = 'images/avatar/';    
    $target_dir_4_upload = '../../../images/avatar/';
    $final_name=basename($_FILES["user_image"]["name"]);
    //check if file exists
    $i=0;
    while (file_exists($target_dir_4_upload.$final_name)) {
      $i++;
      // doi ten file
      $final_name=$i.basename($_FILES["user_image"]["name"]);
    }
        
    //upload file toi folder icon
    $target_file_upload = $target_dir_4_upload . $final_name;
    $target_file = $target_dir . $final_name;
    
    move_uploaded_file($_FILES["user_image"]["tmp_name"], $target_file_upload);
    
    // get link tu file upload for query database
    $query .= "administrator_avatar = '".$target_file."', ";  
} else { $query .= "administrator_avatar = 'images/avatar/user-placeholder.png', ";}    
  // end xu ly file hinh dai dien      

    if (isset($_REQUEST['administrator_email']))         { $query .= "administrator_email  = '" . mysqli_real_escape_string($conn, $_REQUEST['administrator_email'])         . "', "; }
    if (isset($_REQUEST['administrator_password'])) { $query .= "administrator_password = '" . md5(mysqli_real_escape_string($conn, $_REQUEST['administrator_password'])) . "', "; }

    if (isset($_REQUEST['administrator_phone'])) { $query .= "administrator_phone = '" . mysqli_real_escape_string($conn, $_REQUEST['administrator_phone'])         . "', "; }
    if (isset($_REQUEST['status'])) { $query .= "status = '" . mysqli_real_escape_string($conn, $_REQUEST['status'])         . "' "; }    

    $query = mysqli_query($conn, $query);

    if ($query){
      $result  = 'success';
      $message = 'query success';
     
    } else {
      $result  = 'error';
      $message = 'query error';
    }     
  
  } elseif ($job == 'edit_company'){
    
    // Edit company
    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
    } else {

      // update in table table_administrator

      $query = "UPDATE table_administrator SET ";

      if (isset($_REQUEST['administrator_name']))         { $query .= "administrator_name         = '" . mysqli_real_escape_string($conn, $_REQUEST['administrator_name'])         . "', "; }    

// xu ly file hinh dai dien
if (isset($_FILES['user_image']))
{
if( is_uploaded_file($_FILES['user_image']['tmp_name']))
{ 
    // do this, upload file
    $target_dir = 'images/avatar/';
    $target_dir_4_upload = '../../../images/avatar/';
    $final_name=basename($_FILES["user_image"]["name"]);
    //check if file exists
    $i=0;
    while (file_exists($target_dir_4_upload.$final_name)) {
      $i++;
      // doi ten file
        $final_name=$i.basename($_FILES["user_image"]["name"]);
    }
        
    //upload file toi folder icon
    $target_file_upload = $target_dir_4_upload . $final_name;
    $target_file = $target_dir . $final_name;

    // unlink old file avatar if exist and it not the place holder file
    if (file_exists('../../../'.$_REQUEST['administrator_avatar']) && ($_REQUEST['administrator_avatar']!='images/avatar/user-placeholder.png') )
    { 
    unlink('../../../'.$_REQUEST['administrator_avatar']);
    }
    // end handle if already have avatar
    move_uploaded_file($_FILES["user_image"]["tmp_name"], $target_file_upload);    
    // get link tu file upload for query database
    $query .= "administrator_avatar         = '" . $target_file    . "', ";  
} else {}   

}
// end xu ly file hinh dai dien 
     
     
      if (isset($_REQUEST['administrator_phone']))
      { $query .= "administrator_phone = '" . mysqli_real_escape_string($conn, $_REQUEST['administrator_phone']). "', "; }
      if (isset($_REQUEST['administrator_email']))
      { $query .= "administrator_email = '" . mysqli_real_escape_string($conn, $_REQUEST['administrator_email']) . "', "; }

      if (isset($_REQUEST['administrator_password'])){ 
          //check if change administrator_password
          if ($_REQUEST['administrator_password']!='')   
            $query .= "administrator_password = '" . md5(mysqli_real_escape_string($conn, $_REQUEST['administrator_password'])) . "', "; 
        }   

      if (isset($_REQUEST['status']))
      { $query .= "status = '" . mysqli_real_escape_string($conn, $_REQUEST['status']) . "' "; }

      $query .= "WHERE id_administrator = '" . mysqli_real_escape_string($conn, $_REQUEST['id_administrator']) . "'";

      $query  = mysqli_query($conn, $query);

      if (!$query){
        $result  = 'error';
        $message = 'query error';
      } else {
        $result  = 'success';
        $message = 'query success';
      }
    }
    
  } elseif ($job == 'delete_company'){
  
    // Delete company
    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
    } else {
  
    // delete img from folder administrator if exist
  
    $result= mysqli_query($conn,"SELECT * FROM table_administrator WHERE id_administrator = '".$id."' ");   
     
    $row = $result->fetch_assoc();
    $link4delete=$row['administrator_avatar'];
    $link4delete="../../../".$link4delete;
    // delete file if that not the place-holder file
    if (file_exists($link4delete) && ( $link4delete!='../../../images/avatar/user-placeholder.png') )
    {
      unlink($link4delete);
    }

    // end delete img from folder administrator if exist

    // delete from table table_administrator
      $query = "DELETE FROM table_administrator WHERE id_administrator = '".mysqli_real_escape_string($conn, $id)."'";
      $query = mysqli_query($conn, $query);
    // end delete from table table_administrator
      if (!$query){
        $result  = 'error';
        $message = 'query error';
      } else {
        $result  = 'success';
        $message = 'query success';
      }
    }
  
  }
  
// Close database connection
  mysqli_close($conn);

}

// Prepare data
$data = array(
  "result"  => $result,
  "message" => $message,
  "data"    => $mysql_data
);

// Convert PHP array to JSON array
$json_data = json_encode($data);
print $json_data;
?>