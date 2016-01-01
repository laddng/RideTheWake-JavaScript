			var currentTime = new Date();
			var startTime = shuttleLines[lineID][9];
			var endTime = shuttleLines[lineID][10];
			var startDate = shuttleLines[lineID][11];
			var endDate = shuttleLines[lineID][12];

			if((currentTime >= startTime && currentTime <= endTime) && (currentTime >= startDate && currentTime <= endDate))
			{
				document.getElementById("green").style.display = "none";
			}
			else
			{
				document.getElementById("red").style.display = "inline";
			}