<div>
    <div class="flex flex-col lg:flex-row items-center lg:items-start justify-center gap-4 ">
        <div class="card shadow-xl">
            <div class="card-body">
                <table class="">
                    <tr>
                        <th class="text-center">Field</th>
                        <th class="text-center">Value</th>
                    </tr>
                    @foreach ($keyToHeaders as $key => $header)
                        @if (isset($record[$key]))
                            <tr class="hover:bg-secondary">
                                <td>{{ $header }}:</td>
                               <td class="px-4">
                                <strong>
                                    <livewire:copy-element 
                                        :value="$record[$key]"
                                    ></livewire:copy-element>
                                </strong>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td colspan="2" class="text-sm text-gray-600">
                            Tip: Click on a value to copy it
                        </td>
                    </tr>
                </table>
                {{-- <div class="card-actions">
                    <button wire:click="copyAsSpreadsheet" class="btn btn-primary">Copy as Spreadsheet</button>
                </div> --}}
            </div>
        </div>
    </div>
</div>
