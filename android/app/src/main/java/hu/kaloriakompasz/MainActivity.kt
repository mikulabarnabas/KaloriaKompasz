package hu.kaloriakompasz

import android.content.Intent
import android.os.Bundle
import android.util.Log
import androidx.health.connect.client.HealthConnectClient
import androidx.health.connect.client.records.StepsRecord
import androidx.health.connect.client.request.ReadRecordsRequest
import androidx.health.connect.client.time.TimeRangeFilter
import androidx.lifecycle.lifecycleScope
import com.getcapacitor.BridgeActivity
import com.getcapacitor.JSObject
import com.getcapacitor.Plugin
import com.getcapacitor.PluginCall
import com.getcapacitor.PluginMethod
import com.getcapacitor.annotation.CapacitorPlugin
import kotlinx.coroutines.launch
import java.time.ZoneId

import com.samsung.android.sdk.health.data.HealthDataService
import com.samsung.android.sdk.health.data.permission.Permission
import com.samsung.android.sdk.health.data.permission.AccessType
import com.samsung.android.sdk.health.data.request.LocalTimeFilter
import com.samsung.android.sdk.health.data.request.LocalTimeGroup
import com.samsung.android.sdk.health.data.request.LocalTimeGroupUnit
import com.samsung.android.sdk.health.data.request.DataType
import com.samsung.android.sdk.health.data.request.DataTypes

import java.time.LocalDate
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

import ee.forgr.capacitor.social.login.GoogleProvider
import ee.forgr.capacitor.social.login.ModifiedMainActivityForSocialLoginPlugin
import ee.forgr.capacitor.social.login.SocialLoginPlugin

class MainActivity : BridgeActivity(), ModifiedMainActivityForSocialLoginPlugin {

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)

        if (requestCode >= GoogleProvider.REQUEST_AUTHORIZE_GOOGLE_MIN &&
            requestCode < GoogleProvider.REQUEST_AUTHORIZE_GOOGLE_MAX) {

            val pluginHandle = bridge.getPlugin("SocialLogin")
            if (pluginHandle == null) {
                Log.i("Google Activity Result", "SocialLogin login handle is null")
                return
            }

            val plugin = pluginHandle.instance
            if (plugin !is SocialLoginPlugin) {
                Log.i("Google Activity Result", "SocialLogin plugin instance is not SocialLoginPlugin")
                return
            }

            plugin.handleGoogleLoginIntent(requestCode, data)
        }
    }

    override fun IHaveModifiedTheMainActivityForTheUseWithSocialLoginPlugin() {
        // Leave empty
    }

    public override fun onCreate(savedInstanceState: Bundle?) {
        registerPlugin(HealthConnectBridgePlugin::class.java)
        registerPlugin(SamsungHealthPlugin::class.java)
        super.onCreate(savedInstanceState)
    }

    @CapacitorPlugin(name = "HealthConnectBridge")
    open class HealthConnectBridgePlugin : Plugin() {

        private val healthConnectClient by lazy { HealthConnectClient.getOrCreate(context) }

        private val permissions = setOf(
            androidx.health.connect.client.permission.HealthPermission.getReadPermission(StepsRecord::class)
        )

        @PluginMethod
        fun getHealthStatus(call: PluginCall) {
            val availabilityStatus = HealthConnectClient.getSdkStatus(context)
            val ret = JSObject()
            ret.put("status", availabilityStatus)

            if (availabilityStatus == 3) {
                val providerPackageName = "com.google.android.apps.healthdata"
                ret.put("updateUri", "market://details?id=$providerPackageName")
            }
            call.resolve(ret)
        }

        @PluginMethod
        fun requestHealthPermissions(call: PluginCall) {
            activity.lifecycleScope.launch {
                try {
                    val intent = Intent(HealthConnectClient.ACTION_HEALTH_CONNECT_SETTINGS)

                    activity.startActivity(intent)

                    call.resolve()
                } catch (e: Exception) {
                    Log.e("HealthConnect", "Hiba az ablak nyitásakor", e)
                    call.reject("Nem sikerült megnyitni az engedélykezelőt: ${e.localizedMessage}")
                }
            }
        }

        @PluginMethod
        fun checkHealthPermissions(call: PluginCall) {
            activity.lifecycleScope.launch {
                try {
                    val granted = healthConnectClient.permissionController.getGrantedPermissions()
                    val hasAll = granted.containsAll(permissions)

                    val ret = JSObject()
                    ret.put("granted", hasAll)
                    call.resolve(ret)
                } catch (e: Exception) {
                    call.reject("Hiba az engedélyek ellenőrzésekor: ${e.message}")
                }
            }
        }

        @PluginMethod
        fun openHealthSettings(call: PluginCall) {
            try {
                val intent = Intent("android.settings.HEALTH_CONNECT_SETTINGS")
                intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                context.startActivity(intent)
                call.resolve()
            } catch (e: Exception) {
                call.reject("Nem sikerült megnyitni: ${e.message}")
            }
        }

        @PluginMethod
        fun getSteps(call: PluginCall) {
            val dateString = call.getString("date")

            if (dateString == null) {
                call.reject("A 'date' paraméter megadása kötelező (YYYY-MM-DD formátumban).")
                return
            }

            activity.lifecycleScope.launch {
                try {
                    val localDate = LocalDate.parse(dateString)
                    val zoneId = ZoneId.systemDefault()

                    val startOfDay = localDate.atStartOfDay(zoneId).toInstant()
                    val endOfDay = localDate.plusDays(1).atStartOfDay(zoneId).toInstant()

                    val response = healthConnectClient.readRecords(
                        ReadRecordsRequest(
                            recordType = StepsRecord::class,
                            timeRangeFilter = TimeRangeFilter.between(startOfDay, endOfDay)
                        )
                    )

                    val totalSteps = response.records.sumOf { it.count }

                    val ret = JSObject()
                    ret.put("steps", totalSteps)
                    ret.put("date", dateString)
                    call.resolve(ret)

                } catch (e: Exception) {
                    Log.e("HealthConnect", "Hiba a dátum feldolgozásakor", e)
                    call.reject("Hiba a lépések lekérdezésekor: ${e.message}")
                }
            }
        }

    }

    @CapacitorPlugin(name = "SamsungHealthCustom")
    open class SamsungHealthPlugin : com.getcapacitor.Plugin() {

        @PluginMethod
        fun checkSamsungPermissions(call: PluginCall) {
            activity.lifecycleScope.launch {
                try {
                    val store = HealthDataService.getStore(context)
                    val requiredPermissions = setOf(Permission.of(DataTypes.STEPS, AccessType.READ))

                    val grantedResult = store.getGrantedPermissions(requiredPermissions)
                    val hasAll = grantedResult.containsAll(requiredPermissions)

                    val ret = JSObject()
                    ret.put("granted", hasAll)
                    call.resolve(ret)
                } catch (e: Exception) {
                    call.reject("Hiba az ellenőrzéskor: ${e.localizedMessage}")
                }
            }
        }

        @PluginMethod
        fun requestSamsungPermissions(call: PluginCall) {
            activity.lifecycleScope.launch {
                try {
                    val store = HealthDataService.getStore(context)
                    val requiredPermissions = setOf(Permission.of(DataTypes.STEPS, AccessType.READ))

                    // Ez nyitja meg a Samsung Health engedélykérő Activity-t
                    val result = store.requestPermissions(requiredPermissions, activity)
                    val hasAll = result.containsAll(requiredPermissions)

                    val ret = JSObject()
                    ret.put("granted", hasAll)
                    call.resolve(ret)
                } catch (e: Exception) {
                    call.reject("Hiba az engedélykéréskor: ${e.localizedMessage}")
                }
            }
        }

        @PluginMethod
        fun getSteps(call: PluginCall) {
            val dateString = call.getString("date") ?: return call.reject("Dátum hiányzik")

            activity.lifecycleScope.launch {
                try {
                    val store = HealthDataService.getStore(context)
                    val targetDate = LocalDate.parse(dateString)

                    val totalSteps = withContext(Dispatchers.IO) {
                        val stepsRequest = DataType.StepsType.TOTAL.requestBuilder
                            .setLocalTimeFilterWithGroup(
                                LocalTimeFilter.of(targetDate.atStartOfDay(), targetDate.plusDays(1).atStartOfDay()),
                                LocalTimeGroup.of(LocalTimeGroupUnit.DAILY, 1)
                            ).build()

                        val response = store.aggregateData(stepsRequest)
                        var sum = 0L
                        response.dataList.forEach { sum += it.value ?: 0L }
                        sum
                    }

                    val ret = JSObject()
                    ret.put("steps", totalSteps)
                    call.resolve(ret)
                } catch (e: Exception) {
                    call.reject("Hiba: ${e.localizedMessage}")
                }
            }
        }
    }
}
