<?php

namespace App\Models;

use App\Traits\HasCompany;
use App\Traits\IconTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Holiday
 *
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property string $hashname
 * @property string $size
 * @property string|null $description
 * @property string|null $google_url
 * @property string|null $dropbox_link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $added_by
 * @property int|null $last_updated_by
 * @property-read mixed $file_url
 * @property-read mixed $icon
 * @property-read \App\Models\Lead $lead
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereDropboxLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereGoogleUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereHashname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereLastUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereUserId($value)
 * @property int $investment_id
 * @property-read \App\Models\Investment $investment
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereInvestmentId($value)
 * @property int|null $company_id
 * @property-read \App\Models\Company|null $company
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereCompanyId($value)
 * @property int $default_status
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentFiles whereDefaultStatus($value)
 * @mixin \Eloquent
 */
class InvestmentFiles extends BaseModel
{

    use HasCompany;
    use IconTrait;

    const FILE_PATH = 'investments';

    protected $fillable = [];

    protected $guarded = ['id'];
    protected $table = 'investment_files';

    protected $appends = ['file_url', 'icon'];

    public $timestamps = false;

    public function getFileUrlAttribute()
    {
        return asset_url_local_s3(Investment::FILE_PATH . '/' . $this->hashname);
    }

    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }

}
