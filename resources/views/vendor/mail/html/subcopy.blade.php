<table class="subcopy-main-container" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
    <tbody>
        <tr>
            <td>
                <table class="subcopy-main-table table-stack" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                    <tbody>
                        <tr>
                            <td class="subcopy-main-desktop-table-td column" width="100%">
                                <table class="subcopy-main-desktop-table-td-table" width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td class="subcopy-main-desktop-table-td-table-td">
                                                {{ Illuminate\Mail\Markdown::parse($slot) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>