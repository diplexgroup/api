<tr>
    <td width="50%">{{$label}}</td>
    @if ($doc->isSelect($field))
        <td><select class="form-control" name="forms[{{$field}}]">
                @foreach ($doc->getOptions($field) as $value=>$item)
                    <option @if ($doc->$field === $value) selected @endif value="{{$value}}">{{$item}}</option>
                @endforeach
            </select>
        </td>
    @else
        <td><input name="forms[{{$field}}]" class="form-control" value="{{$doc->getInputAttr($field)}}"/></td>
    @endif
</tr>