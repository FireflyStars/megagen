<div class="panel">
    <h3><i class="icon-list-ul"></i> {l s='Discount' mod='sssdiscount'}
	<span class="panel-heading-action">
		
	</span>
    </h3>



  <div class="table-responsive">

  	<form id="discountsfrm" method="post">

  	 {foreach from=$categories item=category} 

	  	 <div class="card">

	  	 <div class="card-header">{$category.name}</div>
		  <div class="card-body">


		  		 {foreach from=$category.subcategory item=subcategory} 


		  		 	<table style="    width: 100%;">
		  		 		<tr>
		  		 			<td width="40%">{$subcategory.name}</td>
		  		 			<td><input type="text" name="discount[{$subcategory.id_category}]" value="{$subcategory.value}"></td>
		  		 		</tr>
		  		 	</table>


		  		  {/foreach}
		   
		  </div>
		</div>


  	  {/foreach}

  	   <input type="submit" value="Submit">

                
  	</form>

  </div>




</div>
<style type="text/css">
	.card {
    position: relative;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0,0,0,.125);
    border-radius: .25rem;
    margin-bottom: 10px;
}

.card-body {
    -webkit-box-flex: 1;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    padding: 1.25rem;
}
.card-header {
    padding: .75rem 1.25rem;
    margin-bottom: 0;
    background-color: rgba(0,0,0,.03);
    border-bottom: 1px solid rgba(0,0,0,.125);
}
</style>