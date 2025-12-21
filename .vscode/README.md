# Cấu hình VS Code cho Claude Sonnet 4.5

## 📋 Tổng quan

Thư mục này chứa cấu hình VS Code để sử dụng Claude Sonnet 4.5 và các công cụ AI coding assistant khác.

## 🤖 AI Coding Assistants

### Option 1: Continue.dev (Khuyên dùng)

**Continue.dev** là một extension mã nguồn mở hỗ trợ nhiều AI models bao gồm Claude Sonnet 4.5.

#### Cài đặt:

1. Cài đặt extension: [Continue - Codestral, Claude, and more](https://marketplace.visualstudio.com/items?itemName=continue.continue)
2. Lấy API key từ [Anthropic Console](https://console.anthropic.com/)
3. Mở Continue sidebar và chọn model **"Claude Sonnet 4.5"**
4. Nhập API key khi được yêu cầu

#### Models có sẵn:
- ✅ **Claude Sonnet 4.5** (Model mới nhất - 2024)
- ✅ **Claude 3.5 Sonnet** (Fast & accurate)
- ✅ **Claude 3 Opus** (Most capable)

### Option 2: Cline (Claude Dev)

**Cline** (trước đây là Claude Dev) là extension chuyên dụng cho Claude API.

#### Cài đặt:

1. Cài đặt extension: [Cline](https://marketplace.visualstudio.com/items?itemName=saoudrizwan.claude-dev)
2. Set environment variable: `ANTHROPIC_API_KEY=your_api_key_here`
3. Chọn model `claude-sonnet-4.5-20241022` trong settings

## 🔧 Setup API Key

### Cách 1: Environment Variable (Bảo mật nhất)

**Windows:**
```cmd
setx ANTHROPIC_API_KEY "your_api_key_here"
```

**Linux/Mac:**
```bash
echo 'export ANTHROPIC_API_KEY="your_api_key_here"' >> ~/.bashrc
source ~/.bashrc
```

### Cách 2: Continue Config File

Edit file `.continue/config.json`:
```json
{
  "models": [
    {
      "title": "Claude Sonnet 4.5",
      "provider": "anthropic",
      "model": "claude-sonnet-4.5-20241022",
      "apiKey": "your_api_key_here"
    }
  ]
}
```

### Cách 3: VS Code Settings

Mở Command Palette (`Ctrl+Shift+P`) → `Preferences: Open User Settings (JSON)`:
```json
{
  "cline.anthropic.apiKey": "your_api_key_here"
}
```

## 📚 Tài liệu

### Claude Sonnet 4.5 Model Name

Tên model chính xác để sử dụng:
- `claude-sonnet-4.5-20241022` (Model mới nhất tính đến 12/2024)
- `claude-3-5-sonnet-20241022` (Phiên bản trước)
- `claude-3-opus-20240229` (Model mạnh nhất của Claude 3)

### Kiểm tra model có sẵn

```bash
curl https://api.anthropic.com/v1/models \
  -H "x-api-key: $ANTHROPIC_API_KEY" \
  -H "anthropic-version: 2023-06-01"
```

## 🎯 Sử dụng

### Continue.dev

1. Mở Continue sidebar (biểu tượng Continue trong Activity Bar)
2. Chọn model "Claude Sonnet 4.5" từ dropdown
3. Bắt đầu chat hoặc sử dụng shortcuts:
   - `Cmd/Ctrl + L`: Open Continue chat
   - `Cmd/Ctrl + I`: Inline edit
   - `Cmd/Ctrl + Shift + L`: Quick chat

### Cline

1. Mở Command Palette (`Ctrl+Shift+P`)
2. Chạy `Cline: Open`
3. Model sẽ tự động sử dụng `claude-sonnet-4.5-20241022` theo settings

## ⚙️ Extensions được khuyên dùng

File `extensions.json` chứa danh sách extensions được khuyên dùng cho project này:

**AI Assistants:**
- Continue (continue.continue)
- Cline (saoudrizwan.claude-dev)

**PHP/Laravel:**
- Intelephense
- Laravel Blade
- Laravel Extra Intellisense

**JavaScript/Frontend:**
- ESLint
- Prettier
- Tailwind CSS IntelliSense

Khi mở project lần đầu, VS Code sẽ gợi ý cài đặt các extensions này.

## 🔍 Troubleshooting

### "Cannot find Claude Sonnet 4.5"

**Giải pháp:**
1. Kiểm tra API key đã được set chưa
2. Kiểm tra tên model: `claude-sonnet-4.5-20241022`
3. Restart VS Code sau khi thay đổi settings
4. Update extension lên version mới nhất

### "Invalid API Key"

**Giải pháp:**
1. Verify API key tại: https://console.anthropic.com/
2. Kiểm tra API key có quyền truy cập Claude API
3. Đảm bảo không có khoảng trắng trong API key

### "Model not available"

**Giải pháp:**
1. Kiểm tra account có access đến Claude Sonnet 4.5
2. Một số regions có thể chưa có model này
3. Thử sử dụng `claude-3-5-sonnet-20241022` thay thế

## 📞 Hỗ trợ

- [Continue Documentation](https://continue.dev/docs)
- [Cline Documentation](https://github.com/saoudrizwan/claude-dev)
- [Anthropic API Docs](https://docs.anthropic.com/)

---

**Lưu ý:** API keys là thông tin bảo mật. Không commit vào Git. Sử dụng environment variables hoặc config files local.
