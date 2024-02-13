<div class="modal fade" id="assPrivModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Assign Privilege</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="/profile" id="ass-priv-form">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover dataTable js-exportable" width="100%"
                            id="priv-table">
                            <thead>
                                <th>Mod ID</th>
                                <th> Module Names </th>
                                <th> View</th>
                            </thead>
                            <tbody>
                                {{-- data is fetched here using Ajax/js_fetch --}}
                            </tbody>
                        </table>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function updatePrivilege(modID, userID, switchToggle) {

        let data = {
            modid: modID,
            userid: userID,
            privType: switchToggle.dataset.privType,
            status: Number(switchToggle.checked)
        };

        let status = Number(switchToggle.checked);
        let privType = switchToggle.dataset.privType;

        var formdata = new FormData()
        formdata.append("modID", modID);
        formdata.append("userID", userID);
        formdata.append("status", status);
        formdata.append("privType", privType);

        fetch(`${APP_URL}/api/users/update_priv`, {
            method: "POST",
            body: formdata,
        }).then(function (res) {
            return res.json()
        }).then(function (data) {

            privTable.ajax.reload(false, null);
        });


    }

</script>
