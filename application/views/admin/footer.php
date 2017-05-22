    <footer>
        <p>Copyright © . ALL RIGHTS RESERVED.</p>
    </footer>
    <script type="text/javascript">
    $("[rel=tooltip]").tooltip();
    
    $(function() {
        $('.demo-cancel-click').click(function(){return false;});
    });
	$(function() {
            
        $page = $("h1.page-title").html();
      
        if(!$page) { $page = ""}
      
        $('.demo-cancel-click').click(function(){return false;});
        document.title = "Tabula " + $page;
    });
	
    </script>
<!--    <script src="<?php //echo $this->config->item('assets');?>lib/aes.js"></script>
<script src="<?php //echo $this->config->item('assets');?>lib/aes-json-format.js"></script>
<script>
 $("#LoginForm").submit(function(event){
// alert('hi');
      var encrypted_data = CryptoJS.AES.encrypt(JSON.stringify($("#txtPassword").val()), 
   '<?php //echo $this->config->item('encryption_key') ?>', {format: CryptoJSAesJson}).toString();
        
  $("#txtPassword").val(encrypted_data);
});

</script>-->