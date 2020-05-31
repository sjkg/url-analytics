<?php 
    include("header.php");
?>
    <div class="content">
        <h4 class="text-center">URL SHORTNER AND ANALYSING</h4>
        <div class="url-analytics">
            <div class ="url-traffic-form-section">
                <form action="" class="url-analytics-form">
                    <div class="form-group">
                        <label for="url">URL:</label>
                        <input type="text" class="form-control" id="url" placeholder="Enter url" name="url" required>
                        <span class="error"></span>
                    </div>
                    <div class="form-group">
                        <button type="button" id="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
            <div class ="url-analytics-details" id = "url_analytics_details">            
            </div>
        </div>
    </div>
<?php 
    include("footer.php");
?>