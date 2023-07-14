@if($errors->any())
<ul style="color:red">
    @foreach($errors->all() as $err)
    <li>{{$err}}</li>
    @endforeach
</ul>
@endif

<form action="" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{$user[0]['id']}}">
    <input type="password" name="password" placeholder="new password">
    <br><br>
    <input type="password" name="password_confirmation" placeholder="confirm password" id="">
    <br><br>
    <input type="submit" value="reset">

</form>