<?php
//require_once("../inc/dbfunctions.php");
/* session_put($var_array - array of two dimensions 1 for field_name and 1 for its value) */
function duplicate_header($sess_uid,$dbconn,$wk_header_id,$duplicate_header,$duplicate_products,$duplicate_lines,$sess_user_prefix,$sess_org_id,$header_status=null,$header_number=null)
{
    $select_query="SELECT order_headers.header_id, order_headers.org_id, order_headers.user_id,
                                   order_headers.last_updated_by, order_headers.created_by,
                                   order_headers.salesrep_id, order_headers.account_manager_id,
                                   order_headers.project, order_headers.base, order_headers.height,
                                   order_headers.customer_id, order_headers.status_id,
                                   order_headers.supplier_id, order_headers.prefix,
                                   order_headers.header_number, order_headers.header_version,
                                   order_headers.delivery_id, order_headers.contact_id,
                                   order_headers.run_on_perc, order_headers.notes,
                                   order_headers.header_date, order_products.product_id,
                                   order_products.description, order_products.line_number,
                                   order_products.quantity, order_products.last_update_date,
                                   order_products.last_updated_by, order_products.creation_date,
                                   order_products.created_by, order_products.version,
                                   order_products.book_weight, order_products.copies_per_carton,
                                   order_products.estimate_ref, order_products.unit_price,
                                   order_products.run_on_price, order_lines.line_id, order_lines.group_seq,
                                   order_lines.GROUP_ID, order_lines.item_id, order_products.header_id,
                                   order_lines.uom, order_lines.quantity, order_lines.saving,
                                   order_lines.last_update_date, order_lines.last_updated_by,
                                   order_lines.creation_date, order_lines.created_by, order_lines.version,
                                   order_lines.first_print_startup, order_lines.reprint_startup,
                                   order_lines.group_type_id, order_lines.line_group_id,
                                   order_lines.product_id, order_headers.plant_account_manager_id,
                                   order_lines.saving, order_lines.optional, order_lines.text_field, order_lines.supplied,
                                   order_status.price_list_flag, order_lines.price_line_id,
                                   order_status.duplicate_status_id, order_headers.currency_id
                            FROM order_headers, order_products, order_lines, order_status
                            WHERE (    (order_headers.header_id = order_products.header_id)
                            AND (order_headers.status_id = order_status.status_id)
                            AND (order_products.product_id = order_lines.product_id)
                            AND (order_headers.header_id=$wk_header_id)
                            AND (order_lines.created_by <> 99)) order by order_products.product_id";
             //print("$select_query<br>");
             $query_result=ociparse($dbconn,$select_query);
             ociexecute($query_result,OCI_DEFAULT);
             $wk_product_id=0;
             while (ocifetchinto($query_result,&$row)) {
               if ($duplicate_header) {
                   $curr_date=getdate();
                   $new_number=get_number($dbconn,"ORDERS",$sess_org_id,$sess_user_prefix,$curr_date[year]);
                   $insert_query="insert into order_headers
                                   (order_headers.header_id, order_headers.org_id, order_headers.user_id,
                                   order_headers.last_updated_by, order_headers.created_by,
                                   order_headers.salesrep_id, order_headers.account_manager_id,
                                   order_headers.project, order_headers.base, order_headers.height,
                                   order_headers.customer_id, order_headers.status_id,
                                   order_headers.supplier_id, order_headers.prefix,
                                   order_headers.header_number, order_headers.header_version,
                                   order_headers.delivery_id, order_headers.contact_id,
                                   order_headers.run_on_perc, order_headers.notes,
                                   order_headers.header_date, order_headers.plant_account_manager_id,
                                   order_headers.header_year, order_headers.currency_id)
                            values (headers_seq.NextVal, $sess_org_id, $sess_uid,
                                   $sess_uid, $sess_uid,
                                   $row[5], $row[6],
                                   '$row[7]', $row[8], $row[9],
                                   $row[10], '$row[60]' ,
                                   $row[12], '$sess_user_prefix',
                                   $new_number, 0,
                                   $row[16], $row[17],
                                   $row[18], '$row[19]',
                                   SYSDATE , $row[53], $curr_date[year], $row[61])";
                   // close changed price list if status_id of the order to duplicate is 0 (PRICE LIST)
                   if ($row[11]==0) {
                       $close_query="update order_headers set status_id = 101 where project='$row[7]' and user_id=$sess_uid";
                       $close_result=ociparse($dbconn,$close_query);
                       ociexecute($close_result,OCI_DEFAULT);
                   }
                   $insert_result=ociparse($dbconn,$insert_query);
                   if (ociexecute($insert_result,OCI_DEFAULT)) {
                       ocicommit($dbconn);
                   } else {
                        OCIRollback($dbconn);
                   }
                   $duplicate_header=false;
                   $new_header_query="select last_number, sequence_owner from all_sequences where sequence_name='HEADERS_SEQ' and sequence_owner='LEGONLINE'";
                   $new_header_result=ociparse($dbconn,$new_header_query);
                   ociexecute($new_header_result,OCI_DEFAULT);
                   ocifetchinto($new_header_result,&$new_row);
                   $new_header_id=--$new_row[0];
               }
               if ($duplicate_products AND ($wk_product_id <> $row[21])) {
                   $wk_product_id=$row[21];
                   $insert_query="insert into order_products (order_products.header_id, order_products.product_id,
                                              order_products.description, order_products.line_number,
                                              order_products.quantity, order_products.last_update_date,
                                              order_products.last_updated_by, order_products.creation_date,
                                              order_products.created_by, order_products.version,
                                              order_products.book_weight, order_products.copies_per_carton,
                                              order_products.estimate_ref, order_products.unit_price,
                                              order_products.run_on_price)
                                         values
                                              (headers_seq.CurrVal, products_seq.NextVal,
                                              '$row[22]', $row[23],
                                              0, sysdate,
                                              $sess_uid, sysdate,
                                              $sess_uid, $row[29],
                                              $row[30], $row[31],
                                              '$row[32]', 0,
                                              0)";
                   $insert_result=ociparse($dbconn,$insert_query);
                   ociexecute($insert_result,OCI_DEFAULT);
                   ocicommit($dbconn);
               }

               if ($duplicate_lines) {
                      // for creation of order from price list
                      if ($row[58]=='Y') {
                          if ($row[11]==0) {
                              $wk_price_line_id=$row[35];
                          } else {
                              $wk_price_line_id=$row[59];
                          }
                      } else {
                          $wk_price_line_id=null;
                      }
                      $saving_value=$row[42]==2?1:$row[42];
                      $optional_value=$row[55]==2?1:$row[55];
                      $insert_query="insert into order_lines (order_lines.line_id, order_lines.group_seq,
                                              order_lines.GROUP_ID, order_lines.item_id,
                                              order_lines.uom, order_lines.quantity, order_lines.saving,
                                              order_lines.last_update_date, order_lines.last_updated_by,
                                              order_lines.creation_date, order_lines.created_by, order_lines.version,
                                              order_lines.first_print_startup, order_lines.reprint_startup,
                                              order_lines.group_type_id, order_lines.line_group_id,
                                              order_lines.product_id, order_lines.optional, order_lines.text_field,
                                              order_lines.supplied,order_lines.price_line_id)
                                         values
                                              (lines_seq.NextVal, '$row[36]',
                                              $row[37], '$row[38]',
                                              '$row[40]', $row[41], '$saving_value',
                                              sysdate,
                                              $sess_uid, sysdate,
                                              $sess_uid, $row[47],
                                              $row[48], $row[49],
                                              $row[50], $row[51],
                                              products_seq.CurrVal, '$optional_value', '$row[56]',
                                              '$row[57]', '$wk_price_line_id' )";
                      $insert_result=ociparse($dbconn,$insert_query);
                      ociexecute($insert_result,OCI_DEFAULT);
                      ocicommit($dbconn);
               }

           }
return $new_header_id;
}

function duplicate_product($dbconn,$sess_uid,$wk_header_id,$wk_product_id) {

$select_query="SELECT order_products.header_id, order_products.product_id,
                      order_products.description, order_products.line_number,
                      order_products.quantity, order_products.last_update_date,
                      order_products.last_updated_by, order_products.creation_date,
                      order_products.created_by, order_products.version,
                      order_products.book_weight, order_products.copies_per_carton,
                      order_products.estimate_ref, order_products.unit_price,
                      order_products.run_on_price, order_lines.product_id,
                      order_lines.line_id, order_lines.group_seq, order_lines.GROUP_ID,
                      order_lines.item_id, order_lines.uom, order_lines.quantity,
                      order_lines.saving, order_lines.last_update_date,
                      order_lines.last_updated_by, order_lines.creation_date,
                      order_lines.created_by, order_lines.version,
                      order_lines.first_print_startup, order_lines.reprint_startup,
                      order_lines.group_type_id, order_lines.line_group_id, order_lines.optional,
                      order_lines.text_field, order_lines.supplied, order_lines.price_line_id
               FROM order_products, order_lines
               WHERE ((order_products.product_id = order_lines.product_id)
               and (order_products.product_id=$wk_product_id)
               AND (order_lines.created_by <> 99))";
$query_result=ociparse($dbconn,$select_query);
ociexecute($query_result,OCI_DEFAULT);
$add_product=TRUE;
while (ocifetchinto($query_result,&$row)) {
    if ($add_product) {
        $add_product=FALSE;
        $insert_query="insert into order_products (order_products.header_id, order_products.product_id,
                                              order_products.description, order_products.line_number,
                                              order_products.quantity, order_products.last_update_date,
                                              order_products.last_updated_by, order_products.creation_date,
                                              order_products.created_by, order_products.version,
                                              order_products.book_weight, order_products.copies_per_carton,
                                              order_products.estimate_ref, order_products.unit_price,
                                              order_products.run_on_price)
                                         values
                                              ($wk_header_id, products_seq.NextVal,
                                              '$row[2]', $row[3],
                                              0, sysdate,
                                              $sess_uid, sysdate,
                                              $sess_uid, $row[9],
                                              $row[10], $row[11],
                                              '$row[12]', 0,
                                              0)";
        $insert_result=ociparse($dbconn,$insert_query);
        ociexecute($insert_result,OCI_DEFAULT);
        ocicommit($dbconn);
    }
    $saving_value=$row[22]==2?1:$row[22];
    $optional_value=$row[32]==2?1:$row[32];
    $insert_query="insert into order_lines (order_lines.line_id, order_lines.group_seq,
                                              order_lines.GROUP_ID, order_lines.item_id,
                                              order_lines.uom, order_lines.quantity, order_lines.saving,
                                              order_lines.last_update_date, order_lines.last_updated_by,
                                              order_lines.creation_date, order_lines.created_by, order_lines.version,
                                              order_lines.first_print_startup, order_lines.reprint_startup,
                                              order_lines.group_type_id, order_lines.line_group_id,
                                              order_lines.product_id,order_lines.optional,
                                              order_lines.text_field, order_lines.supplied, order_lines.price_line_id)
                                         values
                                              (lines_seq.NextVal, '$row[17]',
                                              $row[18], '$row[19]',
                                              '$row[20]', $row[21], '$saving_value',
                                              sysdate,
                                              $sess_uid, sysdate,
                                              $sess_uid, $row[27],
                                              $row[28], $row[29],
                                              $row[30], $row[31],
                                              products_seq.CurrVal,'$optional_value',
                                              '$row[33]', '$row[34]', '$row[35]' )";

    $insert_result=ociparse($dbconn,$insert_query);
    ociexecute($insert_result,OCI_DEFAULT);
    ocicommit($dbconn);
}
return NULL;
}
function delete_product($dbconn,$sess_uid,$wk_header_id,$wk_product_id) {
    $delete_query="delete from order_products where product_id=$wk_product_id";
    $delete_result=ociparse($dbconn,$delete_query);
    ociexecute($delete_result,OCI_DEFAULT);
    ocicommit($dbconn);
return NULL;
}

function delete_header($dbconn,$sess_uid,$wk_header_id) {
    $delete_query="delete from order_headers where header_id=$wk_header_id";
    $delete_result=ociparse($dbconn,$delete_query);
    ociexecute($delete_result,OCI_DEFAULT);
    ocicommit($dbconn);
return NULL;
}
function get_number($dbconn,$cnt_type,$cnt_org_id,$cnt_prefix,$cnt_year,$cnt_number=null) {
    $query="SELECT max(counters.cnt_number)
                   FROM counters
                   WHERE (   (counters.cnt_type = '$cnt_type')
                         AND (counters.org_id = $cnt_org_id)
                         AND (counters.cnt_year = $cnt_year)
                         AND (counters.cnt_prefix = '$cnt_prefix')
                         )
                   GROUP by counters.cnt_type, counters.org_id, counters.cnt_year, counters.cnt_prefix";
    //print("$query<br>");
    $result=ociparse($dbconn,$query);
    ociexecute($result,OCI_DEFAULT);
    if (ocifetchinto($result,&$row)) {
        $new_number=$row[0]+1;
        $update_query="update counters set cnt_number = $new_number WHERE (counters.cnt_type = '$cnt_type')
                                                                          AND (counters.org_id = $cnt_org_id)
                                                                          AND (counters.cnt_year = $cnt_year)
                                                                          AND (counters.cnt_prefix = '$cnt_prefix')";
        $update_result=ociparse($dbconn,$update_query);
        ociexecute($update_result,OCI_DEFAULT);
        ocicommit($dbconn);
        return $new_number;
    } else {
        $insert_query="insert into counters (cnt_type, org_id, cnt_year, cnt_prefix, cnt_number) values
                                            ('$cnt_type', $cnt_org_id, $cnt_year, '$cnt_prefix', 1)";
        $insert_result=ociparse($dbconn,$insert_query);
        ociexecute($insert_result,OCI_DEFAULT);
        ocicommit($dbconn);
        return 1;
    }
    return null;
}
function ChgOrderStatus($sess_uid,$dbconn,$wk_header_id){
    $update_query="update order_headers set last_updated_by=$sess_uid, last_update_date=sysdate,
                                            status_id = (select ost.next_status_id from order_status ost where (ost.status_id = order_headers.status_id) and (ost.next_status_id is not null))
                                        where order_headers.header_id in
                                              (select oh.header_id from order_headers oh, order_status os where oh.header_id=$wk_header_id and
                                                                                                                oh.status_id=os.status_id and
                                                                                                                os.next_status_id is not null)";

    //print("$update_query<br>");
    $update_result=ociparse($dbconn,$update_query);
    ociexecute($update_result,OCI_DEFAULT);
    ocicommit($dbconn);
    return null;
}


function close_order($dbconn,$selection,$price_list_flag=null) {
    if ( (IsSet($selection))  and (strlen($selection) > 0) ) {
        $query=($price_list_flag=='Y')?"update order_headers set status_id = 101 ".$selection:"update order_headers set status_id = 100 ".$selection;
        dbupdate($dbconn,$query,false);
    }
    return null;
}

?>
