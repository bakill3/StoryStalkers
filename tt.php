<input id="btn_ads" class="btn btn-primary" type="button" value="Watch Ad to Unlock" onclick="startRewardVideo()" style="left: 50% !important;top: 50%;position: fixed;transform: translateX(-50%);">
<script src="jquery.min.js"></script>
<script type="text/javascript">
		function startRewardVideo(paramFromJS) {
			Android.startRewardVideoAndroidFunction(paramFromJS);
			$("#btn_ads").hide();
			$("#ul").show();
		}
	</script>