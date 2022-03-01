<table>
    <thead>
        <tr>
            <th>User Report</th>
        </tr>

        <tr></tr>

        <tr>
            <th>Total User</th>
            <td>{{ $summary['total'] }}</td>
        </tr>

        <tr></tr>

        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Created Date</th>
        </tr>
        
    </thead>

    <tbody>
        @forelse ($models as $model)
            <tr>
                <td>{{ $model['name'] }}</td>
                <td>{{ $model['email'] }}</td>
                <td>{{ date('d-m-Y H:i:s', strtotime($model['created_at'])) }}</td>
            </tr>
        @empty
            <tr>
                <td>There is no data</td>
            </tr>
        @endforelse    
    </tbody>

</table>