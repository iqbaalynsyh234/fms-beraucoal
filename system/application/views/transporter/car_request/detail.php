<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<div id="main" style="margin: 20px;">
    <h2>Request Info Details</h2>
    <hr />
    <table style="font-size: 12px;">
    <tr>
    <td>Company</td>
    <td>:</td>
    <td>
        <?php 
        if (isset($data->request_group_name))
        {
            echo $data->request_group_name;   
        }
        else
        {
            echo "-";
        }
        ?>
    </td>
    </tr>
    <tr>
    <td>Vehicle</td>
    <td>:</td>
    <td>
        <?php 
        if (isset($data->request_vehicle_no))
        {
            echo $data->request_vehicle_no;    
        }
        else
        {
            echo "-";
        }
        ?>
    </td>
    </tr>
    <tr>
    <td>Start Date</td>
    <td>:</td>
    <td>
        <?php
        if (isset($data->request_start_date))
        {
            echo $data->request_start_date;
        } 
        else
        {
            echo "-";
        }
        ?>
    </td>
    </tr>
    <tr>
    <td>End Date</td>
    <td>:</td>
    <td>
        <?php
        if (isset($data->request_end_date))
        {
            echo $data->request_end_date;
        } 
        else
        {
            echo "-";
        }
        ?>
    </td>
    </tr>
    
    <tr>
    <td colspan="3"><h2>PIC Info</h2></td>
    </tr>
    
    <tr>
    <td>Name</td>
    <td>:</td>
    <td>
    <?php if (isset($data->request_pic_name))
    {
        echo $data->request_pic_name;  
    }
    else
    {
        echo "-";
    }
    ?>
    </td>
    </tr>
    
    <tr>
    <td>Mobile</td>
    <td>:</td>
    <td>
    <?php
    if (isset($data->request_pic_mobile))
    {
        echo $data->request_pic_mobile;
    }
    else
    {
        echo "-";
    }
    ?>
    </td>
    </tr>
    
    <tr>
    <td>Phone</td>
    <td>:</td>
    <td>
    <?php if (isset($data->request_pic_phone))
    {
        echo $data->request_pic_mobile;
    }
    else
    {
        echo "-";
    }
    ?>
    </td>
    </tr>
    
    <tr>
    <td>Address</td>
    <td>:</td>
    <td>
    <?php
    if (isset($data->request_pic_address))
    {
        echo $data->request_pic_address;
    } 
    else
    {
        echo "-";
    }
    ?>
    </td>
    </tr>
    
    <tr>
    <td>Trip Purpose</td>
    <td>:</td>
    <td>
    <?php
    if (isset($data->request_purpose))
    {
        foreach($trip_purpose as $purpose)
        {
            if ($purpose->trip_purpose_id == $data->request_purpose)
            {
                echo $purpose->trip_purpose_name;       
                }
                }    
                }
                ?> 
    </td>
    </tr>
    
    <tr>
    <td>Create Date</td>
    <td>:</td>
    <td>
    <?php
    if (isset($data->request_create_date))
    {
        echo $data->request_create_date;
    }
    else
    {
        echo "-";
    }
    ?>
    </td>
    </tr>
    
    <tr>
    <td>Status</td>
    <td>:</td>
    <td>
    <?php 
    if (isset($data->request_status))
    {
        foreach($request_status as $req_status)
        {
            if ($req_status->request_status_id == $data->request_status)
            {
                echo $req_status->request_status_name;       
                }
                }    
                }
                ?>
    </td>
    </tr>
    
    <tr>
    <td>Confirm Date</td>
    <td>:</td>
    <td>
    <?php
    if (isset($data->request_confirm_date))
    {
        echo $data->request_confirm_date;
    }
    else
    {
        echo "-";
    }
    ?>
    </td>
    </tr>
    
    </table>
    </div>
</div>