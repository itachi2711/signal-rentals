<table>
    <tr>
        <td class="bg-white">
            <img src="{{$setting->logo_url}}" alt="" style="width: 100px; height: 100px;"/>
        </td>
        <td class="bg-white">
            <h2 class="name">{{$setting->company_name}}</h2>
            <div class="address">{{$setting->physical_address}}</div>
            <div class="address">{{$setting->postal_address}}</div>
            <div>{{$setting->phone}}</div>
            <div><a href="mailto:{{$setting->email}}">{{$setting->email}}</a></div>
            <div><a href="{{$setting->website_url}}">{{$setting->website_url}}</a></div>
        </td>
    </tr>
</table>
<hr/>
