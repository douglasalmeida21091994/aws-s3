<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Contratos - AWS S3</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e2e8f0;
        }
        .container { max-width: 500px; width: 100%; }
        .upload-box {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }
        .header { text-align: center; margin-bottom: 35px; }
        .header-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        }
        .header-icon i { font-size: 36px; color: white; }
        .header h2 { font-size: 26px; color: #fff; margin-bottom: 8px; font-weight: 700; }
        .header p { color: #94a3b8; font-size: 14px; }
        .aws-badges { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
        .badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 16px; background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 20px;
            font-size: 12px; color: #60a5fa; font-weight: 500;
        }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 10px; color: #94a3b8; font-weight: 500; font-size: 14px; }
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b; }
        input[type="text"] {
            width: 100%; padding: 14px 15px 14px 45px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px;
            color: #fff; font-size: 14px; transition: all 0.3s ease;
        }
        input[type="text"]:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        .file-input-wrapper {
            position: relative; border: 2px dashed rgba(255, 255, 255, 0.15);
            border-radius: 12px; padding: 30px; text-align: center;
            transition: all 0.3s ease; cursor: pointer; background: rgba(255, 255, 255, 0.02);
        }
        .file-input-wrapper:hover { border-color: #3b82f6; background: rgba(59, 130, 246, 0.05); }
        .file-input-wrapper.has-file { border-color: #22c55e; background: rgba(34, 197, 94, 0.05); }
        .file-input-wrapper input[type="file"] { position: absolute; left: -9999px; }
        .file-icon { font-size: 40px; color: #64748b; margin-bottom: 15px; }
        .file-input-wrapper.has-file .file-icon { color: #22c55e; }
        .file-text { color: #94a3b8; font-size: 14px; }
        .file-text strong { color: #3b82f6; }
        .file-name {
            margin-top: 15px; padding: 10px; background: rgba(255, 255, 255, 0.05);
            border-radius: 8px; color: #e2e8f0; font-size: 13px; display: none;
        }
        .btn-upload {
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white; border: none; border-radius: 12px;
            font-size: 16px; font-weight: 600; cursor: pointer;
            transition: all 0.3s ease; margin-top: 25px;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
        }
        .btn-upload:hover { transform: translateY(-2px); box-shadow: 0 15px 30px rgba(37, 99, 235, 0.4); }
        .btn-upload:disabled { opacity: 0.5; cursor: not-allowed; }
        .divider { margin: 30px 0; border: none; border-top: 1px solid rgba(255, 255, 255, 0.1); }
        .btn-link {
            display: flex; align-items: center; justify-content: center;
            gap: 10px; color: #94a3b8; text-decoration: none;
            font-size: 14px; font-weight: 500; padding: 12px; border-radius: 10px; transition: 0.3s;
        }
        .btn-link:hover { background: rgba(255, 255, 255, 0.05); color: #fff; }
    </style>
</head>
<body>

<div class="container">
    <div class="upload-box">
        <div class="header">
            <div class="header-icon">
                <i class="fas fa-file-pdf"></i>
            </div>
            <h2>Upload de Contratos</h2>
            <p>Apenas arquivos <strong>PDF</strong> são permitidos</p>
            <div class="aws-badges">
                <span class="badge"><i class="fab fa-aws"></i> S3</span>
                <span class="badge"><i class="fas fa-shield-alt"></i> Secure</span>
            </div>
        </div>

        <form id="uploadForm">
            <div class="form-group">
                <label>Código do Usuário</label>
                <div class="input-wrapper">
                    <i class="fas fa-id-card"></i>
                    <input type="text" id="user_code" placeholder="Ex: 080111700" required>
                </div>
            </div>

            <div class="form-group">
                <label>Arquivo do Contrato (PDF)</label>
                <div class="file-input-wrapper" id="fileWrapper">
                    <input type="file" id="file_input" accept="application/pdf">
                    <div class="file-icon">
                        <i class="fas fa-file-upload"></i>
                    </div>
                    <div class="file-text">
                        <strong>Clique para selecionar</strong><br>
                        Formato aceito: .pdf
                    </div>
                    <div class="file-name" id="fileName"></div>
                </div>
            </div>

            <button type="submit" class="btn-upload" id="btnUpload">
                <i class="fas fa-cloud-upload-alt"></i> Fazer Upload
            </button>
        </form>

        <hr class="divider">

        <a href="listar.php" class="btn-link">
            <i class="fas fa-list-ul"></i>
            Ver Arquivos Armazenados
        </a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>

<script>
    $(document).ready(function() {
        const swalConfig = {
            background: '#1e293b',
            color: '#e2e8f0',
            confirmButtonColor: '#3b82f6'
        };

        // Abre seletor de arquivo
        $('#fileWrapper').on('click', function(e) {
            if (e.target.id !== 'file_input') $('#file_input').click();
        });

        $('#file_input').on('click', e => e.stopPropagation());

        // Validação de tipo de arquivo ao mudar
        $('#file_input').on('change', function() {
            const file = this.files[0];
            
            if (file) {
                // Verifica se a extensão é PDF
                if (file.type !== "application/pdf") {
                    Swal.fire({
                        ...swalConfig,
                        icon: 'warning',
                        title: 'Formato Inválido',
                        text: 'Por favor, selecione apenas arquivos PDF.'
                    });
                    this.value = ""; // Limpa o input
                    $('#fileWrapper').removeClass('has-file');
                    $('#fileName').hide();
                    return;
                }

                $('#fileWrapper').addClass('has-file');
                $('#fileName').html(`<i class="fas fa-file-pdf"></i> ${file.name}`).show();
            } else {
                $('#fileWrapper').removeClass('has-file');
                $('#fileName').hide();
            }
        });

        // Drag & Drop com validação
        $('#fileWrapper').on('dragover', function(e) {
            e.preventDefault();
            $(this).css('border-color', '#3b82f6');
        }).on('dragleave', function() {
            if (!$(this).hasClass('has-file')) $(this).css('border-color', 'rgba(255,255,255,0.15)');
        }).on('drop', function(e) {
            e.preventDefault();
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                if (files[0].type === "application/pdf") {
                    document.getElementById('file_input').files = files;
                    $('#file_input').trigger('change');
                } else {
                    Swal.fire({...swalConfig, icon: 'error', title: 'Erro', text: 'Arraste apenas arquivos PDF.'});
                }
            }
        });

        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            
            const fileInput = $('#file_input')[0];
            if (fileInput.files.length === 0) {
                Swal.fire({...swalConfig, icon: 'warning', text: 'Selecione um arquivo PDF primeiro.'});
                return;
            }

            const formData = new FormData();
            formData.append('user_code', $('#user_code').val());
            formData.append('arquivo', fileInput.files[0]);

            $('#btnUpload').prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin"></i> Enviando...');

            Swal.fire({
                ...swalConfig,
                title: 'Processando...',
                text: 'Enviando contrato para AWS S3',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: 'upload.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp) {
                    if (resp.status == 'ok') {
                        Swal.fire({
                            ...swalConfig,
                            icon: 'success',
                            title: 'Sucesso!',
                            text: 'Contrato PDF armazenado.'
                        }).then(() => window.location.href = 'listar.php');
                    } else {
                        Swal.fire({...swalConfig, icon: 'error', title: 'Erro', text: resp.message});
                    }
                },
                complete: () => {
                    $('#btnUpload').prop('disabled', false).html('<i class="fas fa-cloud-upload-alt"></i> Fazer Upload');
                }
            });
        });
    });
</script>
</body>
</html>