<form action="{{ route('test.perform') }}" method="post">
  @csrf
  <input type="submit" value="SYNC">
</form>
