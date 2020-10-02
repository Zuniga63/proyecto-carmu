<!DOCTYPE html>
<html lang="en">
@include("theme.$theme.header")

<body class="hold-transition sidebar-mini layout-fixed">
  <!-- Site wrapper -->
  <div class="wrapper">
    @include("theme.$theme.navbar")
    @include("theme.$theme.sidebar")
    @include("theme.$theme.content")
  </div>
  @include("theme.$theme.footer")
</body>

</html>