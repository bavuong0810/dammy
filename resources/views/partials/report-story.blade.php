<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Báo cáo lỗi</h5>
            </div>
            <div class="modal-body">
                <div id="frm-modal-donate">
                    <div class="input-donate">
                        <input type="hidden" name="story_id" id="report_story_id" value="{{ $story->id }}">
                        <div class="input-group mb-3">
                            <select name="error" id="error" class="form-select">
                                <option value="Sử dụng từ ngữ thô tục">Sử dụng từ ngữ thô tục</option>
                                <option value="Truyện không chính chủ">Truyện không chính chủ</option>
                                <option value="Nội dung có yếu tố chính trị">Nội dung có yếu tố chính trị</option>
                                <option value="Nội dung xuyên tạc lịch sử">Nội dung xuyên tạc lịch sử</option>
                                <option value="Nội dung không phù hợp với độ tuổi">Nội dung không phù hợp với độ tuổi</option>
                                <option value="Nội dung không phù hợp với độ tuổi">Nội dung tuyên truyền mê tín dị đoan</option>
                                <option value="Lỗi khác">Lỗi khác</option>
                            </select>
                        </div>
                        @if(isset($chapter))
                            <input type="hidden" name="error_chapter" value="{{ $chapter->id }}">
                        @else
                            <div class="input-group mb-3">
                                <select name="error_chapter" id="error_chapter" class="form-select">
                                    <option value="">-- Chọn chapter --</option>
                                    @foreach($list_chapters as $item_c)
                                        <option value="{{ $item_c->id }}">{{ $item_c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="input-group mb-3">
                            <textarea name="error_note" id="error_note" cols="30" rows="4"
                                      placeholder="Mô tả chi tiết lỗi (Nếu có)" class="form-control"></textarea>
                        </div>
                        <div class="mb-3 d-flex justify-content-center">
                            <button type="submit" class="btn btn-danger btn-sm m-2" onclick="reportError()">Gửi báo cáo</button>
                            <button type="button" class="btn btn-secondary btn-sm m-2 close"
                                    data-dismiss="modal" aria-label="Close"
                                    onclick="jQuery('#reportModal').modal('hide')">Huỷ</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
