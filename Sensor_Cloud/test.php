<!DOCTYPE html>
<html>

<head>
    <title>Basic Embed</title>
    
    <script type="text/javascript" 
	    src="https://public.tableau.com/javascripts/api/tableau-2.min.js"></script>
    <script type="text/javascript">
        function initViz(x) {
            var containerDiv = document.getElementById("vizContainer"),
                url = x,
                options = {
                    hideTabs: true,
                    onFirstInteractive: function () {
                        console.log("Run this code when the viz has finished loading.");
                    }
                };
            
            var viz = new tableau.Viz(containerDiv, url, options); 
			console.log(containerDiv);
            // Create a viz object and embed it in the container div.
        }
    </script>
</head>

<body>

    <div id="vizContainer" style="width:800px; height:700px;"></div>  
<script>	initViz("https://public.tableau.com/views/281-02/Sheet1?:embed=y&:display_count=yes&publish=yes");</script>
</body>

</html>
