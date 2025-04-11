<div class="container py-5">
        <div class="content content1">
            <h2 class="text-center mb-4" data-aos="fade-up">Đặt Bàn Ngay</h2>
            <form id="bookingForm" class="row g-3" data-aos="fade-up">
                <div class="col-md-6">
                    <label for="name" class="form-label">Họ và Tên</label>
                    <input type="text" class="form-control custom-input" id="name" required>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Số Điện Thoại</label>
                    <input type="tel" class="form-control custom-input" id="phone" required>
                </div>
                <div class="col-md-6">
                    <label for="date" class="form-label">Ngày</label>
                    <input type="date" class="form-control custom-input" id="date" required>
                </div>
                <div class="col-md-6">
                    <label for="time" class="form-label">Giờ</label>
                    <input type="time" class="form-control custom-input" id="time" required>
                </div>
                <div class="col-md-12">
                    <label for="guests" class="form-label">Số Lượng Khách</label>
                    <input type="number" class="form-control custom-input" id="guests" min="1" required>
                </div>
                <div class="col-md-12">
                    <label for="note" class="form-label">Ghi chú</label>
                    <textarea class="form-control custom-input" id="note" rows="3"></textarea>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-dark btn-round">Đặt Bàn</button>
                </div>
            </form>
        </div>
    </div>