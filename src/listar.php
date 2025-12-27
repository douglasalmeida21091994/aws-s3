<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Contratos - AWS S3</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            padding: 20px;
            color: #e2e8f0;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        .header-content { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
        .header-left h1 { font-size: 28px; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 15px; }
        .header-left h1 i { color: #3b82f6; }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 20px;
            font-size: 12px;
            color: #60a5fa;
            font-weight: 500;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .btn-back:hover { background: rgba(255, 255, 255, 0.1); color: #fff; transform: translateY(-2px); }
        .content-box {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 30px;
        }
        .search-box { margin-bottom: 25px; position: relative; }
        .search-box input {
            width: 100%;
            padding: 14px 20px 14px 50px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #fff;
        }
        .search-box i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #64748b; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: rgba(59, 130, 246, 0.1); }
        th { padding: 16px; color: #94a3b8; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid rgba(59, 130, 246, 0.3); }
        td { padding: 18px 16px; font-size: 14px; color: #cbd5e1; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .user-avatar {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 600;
        }
        .btn-view {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }
        .btn-view:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4); color: #fff; }
        
        /* Modal Style Adjustments */
        .modal-content { border-radius: 16px; overflow: hidden; }
        .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <h1><i class="fas fa-cloud"></i> Contratos Armazenados</h1>
                <div class="aws-badges mt-2">
                    <span class="badge"><i class="fab fa-aws"></i> Amazon S3</span>
                    <span class="badge"><i class="fas fa-database"></i> RDS MySQL</span>
                </div>
            </div>
            <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>

    <div class="content-box">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Buscar por usuário, arquivo ou data...">
        </div>

        <div class="table-container">
            <table id="contractsTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-user"></i> Usuário</th>
                        <th><i class="fas fa-file-alt"></i> Nome do Arquivo</th>
                        <th><i class="fas fa-calendar"></i> Data</th>
                        <th><i class="fas fa-cog"></i> Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $mysql_host = getenv('DB_HOST');
                    $mysql_user = getenv('DB_USER');
                    $mysql_pass = getenv('DB_PASS');
                    $mysql_db   = getenv('DB_NAME');
                    $conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
                    
                    if ($conn->connect_error) {
                        echo '<tr><td colspan="4" class="text-center p-5">Erro de conexão</td></tr>';
                    } else {
                        $conn->set_charset("utf8");
                        $res = $conn->query("SELECT * FROM pront_contratos_prof ORDER BY created_at DESC");
                        while ($row = $res->fetch_assoc()) {
                            $userCode = htmlspecialchars($row['user_code']);
                            $fileName = htmlspecialchars($row['file_name']);
                            $date = (new DateTime($row['created_at']))->format('d/m/Y H:i');
                            $fileKey = urlencode($row['file_key']);
                            $initial = strtoupper(substr($userCode, 0, 1));
                            
                            echo "<tr>
                                    <td><div class='d-flex align-items-center gap-3'><div class='user-avatar'>$initial</div>$userCode</div></td>
                                    <td><i class='fas fa-file-pdf text-danger me-2'></i>$fileName</td>
                                    <td>$date</td>
                                    <td>
                                        <button class='btn-view' onclick=\"previewFile('$fileKey', '$fileName')\">
                                            <i class='fas fa-eye'></i> Visualizar
                                        </button>
                                    </td>
                                  </tr>";
                        }
                        $conn->close();
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="background: #1e293b; border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <h5 class="modal-title" id="modalTitle" style="color: #fff;">Visualizar Documento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 85vh; background: #334155;">
                <iframe id="previewFrame" src="" width="100%" height="100%" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>

<script>
    $(document).ready(function() {
        // Busca em tempo real
        $('#searchInput').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#contractsTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Limpar o SRC do iframe quando o modal fechar (importante para não vazar áudio ou cache)
        $('#previewModal').on('hidden.bs.modal', function () {
            $('#previewFrame').attr('src', '');
        });
    });

    function previewFile(key, fileName) {
        // 1. Atualiza o título
        $('#modalTitle').text(fileName);
        
        // 2. Define o SRC imediatamente
        const url = 'download.php?key=' + key;
        $('#previewFrame').attr('src', url);

        // 3. Abre o modal instantaneamente (sem esperar o onload)
        const modalElement = document.getElementById('previewModal');
        const myModal = bootstrap.Modal.getOrCreateInstance(modalElement);
        myModal.show();

        // 4. Feedback visual rápido (opcional, dura apenas 800ms)
        Swal.fire({
            title: 'Abrindo...',
            background: '#1e293b',
            color: '#e2e8f0',
            timer: 800,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
    }
</script>
</body>
</html>