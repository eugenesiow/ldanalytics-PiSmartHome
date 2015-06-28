<nav class="navbar navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><span class="icon-pismarthome icon-pismarthome-title"></span></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <?php
                    $pageList = Flight::get('pageList');
                    foreach($pageList as $pageName => $pageLink) {
                        if(is_array($pageLink)) {
                            if ($pageName == $activePage)
                                echo "<li class='dropdown active'>";
                            else
                                echo "<li class='dropdown'>";

                            echo "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>".$pageName." <span class='caret'></span></a><ul class='dropdown-menu'>";
                            foreach($pageLink as $subPageName => $subPageLink) {
                                echo "<li><a href='".$subPageLink."'>".$subPageName."</a></li>";
                            }
                            echo "</ul></li>";
                        } else {
                            if ($pageName == $activePage)
                                echo "<li class='active'><a href='" . $pageLink . "'>" . $pageName . "</a></li>";
                            else
                                echo "<li><a href='" . $pageLink . "'>" . $pageName . "</a></li>";
                        }
                    }
                ?>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>