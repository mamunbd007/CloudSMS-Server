
<script>
	$(function(){
	  function loadNum()
	  {  
		$('h1.countdown').load('modules/push/index.php');
		setTimeout(loadNum, 5000); // makes it reload every 5 sec
	  }
	  loadNum(); // start the process...
	});
</script>