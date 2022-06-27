<!-- jquery-->
<script src="public/vvvebjs/js/jquery.min.js"></script>
<script src="public/vvvebjs/js/jquery.hotkeys.js"></script>

<!-- bootstrap-->
<script src="public/vvvebjs/js/popper.min.js"></script>
<script src="public/vvvebjs/js/bootstrap.min.js"></script>

<!-- builder code-->
<script src="public/vvvebjs/libs/builder/builder.js"></script>
<!-- undo manager-->
<script src="public/vvvebjs/libs/builder/undo.js"></script>
<!-- inputs-->
<script src="public/vvvebjs/libs/builder/inputs.js"></script>
<!-- components-->
<script src="public/vvvebjs/libs/builder/components-bootstrap5.js"></script>
<script src="public/vvvebjs/libs/builder/components-widgets.js"></script>


<script>
    $(document).ready(function()  {
        Vvveb.Gui.init();
        Vvveb.FileManager.init();
        Vvveb.SectionList.init();
        var pages =
            [
                {name:"narrow-jumbotron", title:"Jumbotron",  url: "demo/narrow-jumbotron/index.html", file: "demo/narrow-jumbotron/index.html", assets: ['demo/narrow-jumbotron/narrow-jumbotron.css']},
                {name:"album", title:"Album",  url: "demo/album/index.html", file: "demo/album/index.html", folder:"content", assets: ['demo/album/album.css']},

        Vvveb.FileManager.addPages(pages);
        Vvveb.FileManager.loadPage("narrow-jumbotron");
        Vvveb.Breadcrumb.init();
    });
</script>
