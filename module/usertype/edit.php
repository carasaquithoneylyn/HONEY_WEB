 <section class="content">

   <!-- ABADIES SOLUTIONS  -->
   
      <div class="container-fluid">
         <?php check_message(); ?>
        <div class="row">
          <div class="col-12">
          
       <div class="card mb-3">
        <ol class="breadcrumb mb-12">
          <form class="form-inline" method="POST" action="controller.php?action=createpermission" >
           <?php
           @$PK = $_GET['TYPEID'];
           $user = new Usertype();
           $S = $user->single_usertype($PK);
           
           ?>
           <input type="hidden" name="PK" class="form-control" readonly value="<?php  echo (isset($S->PK)) ? $S->PK : '0' ;?>">
                  <div class="form-group mb-2">
                    <table>
                      <tr>
                         <td>Usertype :</td><td><input type="text" class="form-control" readonly value="<?php  echo (isset($S->USERTYPE)) ? $S->USERTYPE : 'Type' ;?>"></td>
                      </tr>
                      <tr>
                         
                      </tr>
                       <tr>
                        
                      </tr>
                      <tr>
                       
                         
                      </tr>
                          
                    </table>

                  </form>
                 
              </ol>
        <div class="card-header">
          <i class="fa fa-user"></i>List of User Permission </div>

           <form action="controller.php?action=editpermission" method="POST">
            <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>

                    <tr>
                      <th> No.</th>
                        <th>Form</th>
                        <th>Description</th>
                        <th>Allow Open</th>
                                            
                    </tr>   
                </thead>
                <tbody>
                 <?php
                 global $mydb;
                 $query = "SELECT `UserPermissionID`, `UserType`, u.`FormID` as 'FORMID', `AllowOpen`,`Form`, `Description` FROM `user_permission` u LEFT JOIN form f ON u.FORMID = f.FORMID WHERE UserType='{$S->USERTYPE}' order by f.FORMID ASC";
                 $mydb->setQuery($query);//
                 $cur = $mydb->loadResultList();
                  $i = 1;
                 foreach ($cur as  $value) {
                 
                    echo "<tr>";
                    echo "<td>". $i ."</td>";
                    echo "<td>". $value->Form ."</td>";
                    echo "<td>". $value->Description ."</td>";
                    if ($value->AllowOpen == 1) {
                     echo '<td>
                     <input type="checkbox" id="'.$value->UserPermissionID.'" data-id="'.$value->UserPermissionID.'"  checked></td>';
                    }else{
                      echo '<td>
                     <input type="checkbox" id="'.$value->UserPermissionID.'" data-id="'.$value->UserPermissionID.'"  ></td>';
                    }
                    
                    echo "</tr>";
                      $i = $i + 1;
                 }
                 ?>
                </tbody>
               
              </table>
                
               
              </div>
             
                      



          
        </form>
        </div>  
      </div>
      </div>
      </div>
    </section>

