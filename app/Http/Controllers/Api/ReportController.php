<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use JasperPHP\JasperPHP;

class ReportController extends Controller
{
    protected $jasper;

    public function __construct()
    {
        $this->jasper = new JasperPHP();
    }

    public function list(Project $project)
    {
        $this->authorize("view", $project);

        return response()->json([
            "reports" => [
                [
                    "type" => "project",
                    "name" => "Relatório do Projeto",
                    "description" => "Informações gerais, status e progresso",
                    "endpoint" => "/api/projects/{$project->id}/reports/project",
                ],
                [
                    "type" => "tasks",
                    "name" => "Relatório de Tarefas",
                    "description" => "Detalhes de todas as tarefas do projeto",
                    "endpoint" => "/api/projects/{$project->id}/reports/tasks",
                ],
                [
                    "type" => "team",
                    "name" => "Relatório de Equipe",
                    "description" => "Performance dos membros da equipe",
                    "endpoint" => "/api/projects/{$project->id}/reports/team",
                ],
            ]
        ]);
    }

    public function projectReport(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        try {
            $fileName = "relatorio_projeto_{$project->id}_" . time();
            $reportPath = base_path('resources/reports/relatorio_projetos.jrxml');
            $outputPath = storage_path("app/reports/$fileName");

            if (!file_exists(storage_path("app/reports"))) {
                mkdir(storage_path("app/reports"), 0755, true);
            }

            $parameters = ['PROJECT_ID' => $project->id];

            $this->jasper->process($reportPath, $outputPath, ['pdf'], $parameters)->execute();

            $pdfPath = $outputPath . '.pdf';
            if (!file_exists($pdfPath)) {
                return response()->json(['error' => 'Erro ao gerar relatório'], 500);
            }

            return response()->download($pdfPath, "relatorio_projeto_{$project->name}.pdf")
                ->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar relatório', 'message' => $e->getMessage()], 500);
        }
    }

    public function tasksReport(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        try {
            $fileName = "relatorio_tarefas_{$project->id}_" . time();
            $reportPath = base_path('resources/reports/relatorio_tarefas.jrxml');
            $outputPath = storage_path("app/reports/$fileName");

            if (!file_exists(storage_path("app/reports"))) {
                mkdir(storage_path("app/reports"), 0755, true);
            }

            $parameters = ['PROJECT_ID' => $project->id];

            $this->jasper->process($reportPath, $outputPath, ['pdf'], $parameters)->execute();

            $pdfPath = $outputPath . '.pdf';
            if (!file_exists($pdfPath)) {
                return response()->json(['error' => 'Erro ao gerar relatório'], 500);
            }

            return response()->download($pdfPath, "relatorio_tarefas_{$project->name}.pdf")
                ->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function teamReport(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        try {
            $fileName = "relatorio_equipe_{$project->id}_" . time();
            $reportPath = base_path('resources/reports/relatorio_equipe.jrxml');
            $outputPath = storage_path("app/reports/$fileName");

            if (!file_exists(storage_path("app/reports"))) {
                mkdir(storage_path("app/reports"), 0755, true);
            }

            $parameters = ['PROJECT_ID' => $project->id];

            $this->jasper->process($reportPath, $outputPath, ['pdf'], $parameters)->execute();

            $pdfPath = $outputPath . '.pdf';
            if (!file_exists($pdfPath)) {
                return response()->json(['error' => 'Erro ao gerar relatório'], 500);
            }

            return response()->download($pdfPath, "relatorio_equipe_{$project->name}.pdf")
                ->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function customReport(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $validated = $request->validate([
            'report_type' => 'required|in:projeto,tarefas,equipe',
            'format' => 'required|in:pdf,xlsx',
        ]);

        $reportType = $validated['report_type'];
        $format = $validated['format'];

        try {
            $fileName = "relatorio_{$reportType}_{$project->id}_" . time();

            $reportMap = [
                'projeto' => 'relatorio_projetos.jrxml',
                'tarefas' => 'relatorio_tarefas.jrxml',
                'equipe' => 'relatorio_equipe.jrxml',
            ];

            $reportPath = base_path("resources/reports/{$reportMap[$reportType]}");
            $outputPath = storage_path("app/reports/$fileName");

            if (!file_exists(storage_path("app/reports"))) {
                mkdir(storage_path("app/reports"), 0755, true);
            }

            $parameters = ['PROJECT_ID' => $project->id];
            $formats = $format === 'pdf' ? ['pdf'] : ['xlsx'];

            $this->jasper->process($reportPath, $outputPath, $formats, $parameters)->execute();

            $fileExtension = $format === 'pdf' ? '.pdf' : '.xlsx';
            $fullPath = $outputPath . $fileExtension;

            if (!file_exists($fullPath)) {
                return response()->json(['error' => 'Erro ao gerar relatório'], 500);
            }

            return response()->download($fullPath, "relatorio_{$reportType}_{$project->name}.$format")
                ->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
