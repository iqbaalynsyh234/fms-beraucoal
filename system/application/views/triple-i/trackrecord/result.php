<script>
function field_onchange()
{
    
}
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
	<?=$navigation;?>
	<div id="main" style="margin: 20px;">
    <br /><br />
		<h2>Total Track Record (<?php echo $total;?>)</h2>
    <br />
        <h3>Total List (<?php echo $total_list;?>)</h3>
        <form name="frmsearch" id="frmsearch">
        <table style="font-size: 12px;">
            <tr>
                <td>Search By</td>
                <td>:</td>
                <td>
                <select name="field" id="field" onchange="javascript: return field_onchange();">
                    <option value="all">All</option>
                    <option value="vehicle">Vehicle</option>
                    <option value="driver">Driver</option>
                    <option value="status">Status</option>
                </select>
                </td>
                <td>
                <input type="text" name="keyword" id="keyword" />
                <select name="select_status" id="select_status">
                    <option value="all">All</option>
                    <option value="complete">Complete</option>
                    <option value="allcomplete">Uncomplete</option>
                </select>
                </td>
                <td><a><input type="submit" name="submit" id="submit" value="Search" /></a></td>
            </tr>
        </table>
        </form>
	</div>
</div>