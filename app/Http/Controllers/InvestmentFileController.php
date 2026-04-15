<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Traits\IconTrait;
use Illuminate\Http\Request;
use App\Helper\Files;
use App\Models\Investment;
use App\Models\InvestmentFiles;
use Illuminate\Support\Facades\File;

class InvestmentFileController extends AccountBaseController
{
    use IconTrait;

    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = 'icon-people';
        $this->pageTitle = 'app.menu.investment';
    }

    /**
     * @param Request $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        if ($request->hasFile('file')) {

            $defaultImage = null;

            foreach ($request->file as $fileData) {
                $file = new InvestmentFiles();
                $file->investment_id = $request->investment_id;

                $filename = Files::uploadLocalOrS3($fileData, InvestmentFiles::FILE_PATH);

                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;
                $file->size = $fileData->getSize();
                $file->save();

                if ($fileData->getClientOriginalName() == $request->default_image) {
                    $defaultImage = $filename;
                }

            }

            $investment = Investment::findOrFail($request->investment_id);
            $investment->default_image = $defaultImage;
            $investment->save();

        }

        return Reply::success(__('messages.fileUploaded'));
    }

    public function updateImages(Request $request)
    {
        $defaultImage = null;

        if ($request->hasFile('file')) {
            foreach ($request->file as $file) {
                $investmentFile = new InvestmentFiles();
                $investmentFile->investment_id = $request->investment_id;
                $filename = Files::uploadLocalOrS3($file, 'investments');
                $investmentFile->filename = $file->getClientOriginalName();
                $investmentFile->hashname = $filename;
                $investmentFile->size = $file->getSize();
                $investmentFile->save();

                if ($investmentFile->filename == $request->default_image) {
                    $defaultImage = $filename;
                }

            }
        }

        $investment = Investment::findOrFail($request->investment_id);
        $investment->default_image = $defaultImage ?: $request->default_image;
        $investment->save();

        return Reply::success(__('messages.fileUploaded'));
    }

    /**
     * @param Request $request
     * @param int $id
     * @return array|void
     */
    public function destroy(Request $request, $id)
    {
        InvestmentFiles::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function download($id)
    {
        $file = InvestmentFiles::findOrFail($id);

        return download_local_s3($file, InvestmentFiles::FILE_PATH . '/' . $file->hashname);
    }

}
